<?php declare(strict_types=1);

namespace Tools\PmgStandard\Sniffs\Methods;

final class MethodStructureSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	private const ENABLED_FOR_PATHNAMES = [
		__DIR__ . '/../../../../apps/web/app/libs/Database/Fluent/Joins.php',
	];

	/** @var array<string> */
	private array $enabledForRealPaths;


	public function __construct()
	{
		$this->enabledForRealPaths = array_map('realpath', self::ENABLED_FOR_PATHNAMES);
	}


	/**
	 * @inheritDoc
	 */
	public function register(): array
	{
		return [T_CLASS];
	}


	/**
	 * @inheritDoc
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPointer): void
	{
		if (!in_array($phpcsFile->getFilename(), $this->enabledForRealPaths, TRUE)) {
			return;
		}
		$methods = [];
		$tokens = $phpcsFile->getTokens();
		while (($stackPointer = $phpcsFile->findNext([T_FUNCTION], $stackPointer + 1)) !== FALSE) {
			if (
				$tokens[$stackPointer - 2]['code'] === T_PUBLIC
				&& $tokens[$stackPointer + 2]['content'] !== '__construct'
			) {
				$methods[] = $tokens[$stackPointer + 2]['content'];
			}
		}
		$incorrect = array_diff_assoc(self::sort($methods), $methods);
		foreach ($incorrect as $method) {
			$phpcsFile->addError(sprintf('Method "%s" is not in correct order.', $method), 0, 'AlphabeticalOrder');
		}
	}


	/**
	 * @param string[] $methods
	 * @return string[]
	 */
	private static function sort(array $methods): array
	{
		sort($methods, SORT_STRING);
		return $methods;
	}

}
