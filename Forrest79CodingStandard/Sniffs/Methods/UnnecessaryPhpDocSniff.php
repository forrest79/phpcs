<?php declare(strict_types=1);

namespace Tools\PmgStandard\Sniffs\Methods;

use PHP_CodeSniffer\Files;
use PHP_CodeSniffer\Sniffs;

final class UnnecessaryPhpDocSniff implements Sniffs\Sniff
{
	/** @var array<string, array<string>> */
	private $classTraits = [];

	/** @var array<string, bool> */
	private static array $isTypeIgnoredCache = [];

	private const IGNORED_NATIVE_TYPES = [
		'array',
		'iterable',
		\Iterator::class,
		\Generator::class,
		\ReflectionClass::class,
		'object',
	];

	private const IGNORED_PHPDOC_TYPES = [
		'class-string',
		'static',
	];


	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [T_CLASS];
	}


	/**
	 * @param int $stackPointer
	 */
	public function process(Files\File $phpcsFile, $stackPointer): void
	{
		$content = @file_get_contents($phpcsFile->getFilename());
		if ($content === FALSE) {
			throw new \RuntimeException(sprintf('Can not read file "%s".', $phpcsFile->getFilename()));
		}
		$className = self::className($content);
		try {
			$class = new \ReflectionClass($className); // reflection runs code
		} catch (\Throwable $e) {
			return; // class does not exist - may occur for old code
		}
		foreach ($class->getMethods() as $method) {
			if ($this->isClassMethod($class, $method)) {
				['return' => $returnDoc, 'param' => $paramDoc] = self::phpDocTypes($method);
				$same = array_intersect(self::nativeParameterTypeHints($method), $paramDoc);
				if ($same !== []) {
					$phpcsFile->addError(sprintf('These @param types in phpDoc for method "%s::%d" are unnecessary: [%s]', $method->getName(), $method->getStartLine(), implode(', ', $same)), 0, 'UnnecessaryParam');
				}
				if (self::isReturnUnnecessary($returnDoc, self::nativeReturnType($method))) {
					$phpcsFile->addError(sprintf('@return type in phpDoc for method "%s::%d" is unnecessary', $method->getName(), $method->getStartLine()), 0, 'UnnecessaryReturn');
				}
			}
		}
	}


	private static function isReturnUnnecessary(?string $docReturn, ?string $nativeReturn): bool
	{
		// is not possible to check if two don't match - nativeReturn is always full path to class
		return $docReturn !== NULL && $nativeReturn !== NULL;
	}


	private function isClassMethod(\ReflectionClass $class, \ReflectionMethod $method): bool
	{
		return !in_array($method->getName(), $this->traitMethods($class), TRUE) && $method->class === $class->getName();
	}


	private static function nativeReturnType(\ReflectionMethod $method): ?string
	{
		$type = $method->getReturnType();
		if ($type !== NULL && !in_array($type->getName(), self::IGNORED_NATIVE_TYPES, TRUE)) {
			return $type->getName();
		}
		return NULL;
	}


	/**
	 * @return string[]
	 */
	private static function nativeParameterTypeHints(\ReflectionMethod $method): array
	{
		$return = [];
		foreach ($method->getParameters() as $parameter) {
			$type = $parameter->getType();
			if ($type !== NULL && !in_array($type->getName(), self::IGNORED_NATIVE_TYPES, TRUE)) {
				$return[] = $parameter->getName();
			}
		}
		return $return;
	}


	/**
	 * @return string[]
	 */
	private function traitMethods(\ReflectionClass $class): array
	{
		if (!isset($this->classTraits[$class->getName()])) {
			$this->classTraits[$class->getName()] = [];
			foreach ($class->getTraits() as $trait) {
				foreach ($trait->getMethods() as $method) {
					$this->classTraits[$class->getName()][] = $method->getName();
				}
			}
		}
		return $this->classTraits[$class->getName()];
	}


	/**
	 * @return array{return: string[]|NULL, param: string[]}
	 */
	private static function phpDocTypes(\ReflectionMethod $method): array
	{
		$phpDoc = $method->getDocComment();
		if ($phpDoc === FALSE) {
			return ['return' => NULL, 'param' => []];
		}
		preg_match_all('~^\s+\* @param (?P<types>[\S]+) \$(?P<names>[\S]+)$~m', $phpDoc, $paramMatches);
		preg_match('~^\s+\* @return (?P<type>[\S]+)$~m', $phpDoc, $returnMatches);
		$return = NULL;
		if (isset($returnMatches['type']) && !self::isTypeIgnored($returnMatches['type'])) {
			$return = $returnMatches['type'];
			$unions = explode('|', $returnMatches['type']);
			if (count($unions) > 1 && array_uintersect(['string', 'int', 'bool', 'float', 'null'], $unions, 'strcasecmp') === []) {
				$return = NULL;
			}
		}
		$params = array_keys(
			array_filter(
				array_combine($paramMatches['names'], $paramMatches['types']),
				static fn (string $type): bool => !self::isTypeIgnored($type),
			),
		);
		return [
			'return' => $return,
			'param' => $params,
		];
	}


	private static function isTypeIgnored(string $type): bool
	{
		if (!isset(self::$isTypeIgnoredCache[$type])) {
			$return = FALSE;
			if (in_array($type, self::IGNORED_PHPDOC_TYPES, TRUE)) {
				$return = TRUE;
			} else {
				foreach (self::IGNORED_PHPDOC_TYPES as $ignoredType) {
					if (preg_match("~^$ignoredType<.+>$~", $type) === 1) {
						$return = TRUE;
					}
				}
			}
			self::$isTypeIgnoredCache[$type] = $return;
		}

		return self::$isTypeIgnoredCache[$type];
	}


	private static function className(string $content): string
	{
		preg_match('~^(final\s|abstract\s)?class\s(?P<class>[\S]+)~m', $content, $classMatches);
		preg_match('~^namespace\s(?P<namespace>[\S]+);~m', $content, $namespaceMatches);
		$className = $classMatches['class'];
		$namespace = $namespaceMatches['namespace'] ?? '';
		return $namespace . '\\' . $className;
	}

}
