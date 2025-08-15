<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Exceptions;

use PHP_CodeSniffer;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

/**
 * @author https://github.com/consistence/coding-standard
 */
final class ExceptionDeclarationSniff implements PHP_CodeSniffer\Sniffs\Sniff
{
	public const CODE_NOT_ENDING_WITH_EXCEPTION = 'NotEndingWithException';
	public const CODE_NOT_CHAINABLE = 'NotChainable';
	public const CODE_INCORRECT_EXCEPTION_DIRECTORY = 'IncorrectExceptionDirectory';

	public string $exceptionsDirectoryName = 'Exceptions';


	/**
	 * @inheritDoc
	 */
	public function register(): array
	{
		return [
			T_CLASS,
			T_INTERFACE,
		];
	}


	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer\Files\File $file, $classPointer): void
	{
		$extendedClassName = $file->findExtendedClassName($classPointer);
		if ($extendedClassName === false) {
			return; //does not extend anything
		}

		if (!StringHelper::endsWith($extendedClassName, 'Exception')) {
			return; // does not extend Exception, is not an exception
		}

		$this->checkExceptionName($file, $classPointer);

		$this->checkExceptionDirectoryName($file, $classPointer);

		$this->checkThatExceptionIsChainable($file, $classPointer);
	}


	private function checkExceptionName(PHP_CodeSniffer\Files\File $file, int $classPointer): void
	{
		$className = ClassHelper::getName($file, $classPointer);
		if (!StringHelper::endsWith($className, 'Exception')) {
			$file->addError(sprintf(
				'Exception class name "%s" must end with "Exception".',
				$className,
			), $classPointer, self::CODE_NOT_ENDING_WITH_EXCEPTION);
		}
	}


	private function checkExceptionDirectoryName(PHP_CodeSniffer\Files\File $file, int $classPointer): void
	{
		$filename = $file->getFilename();

		// normalize path for Windows (PHP_CodeSniffer detects it with backslashes on Windows)
		$filename = str_replace('\\', '/', $filename);

		$pathInfo = pathinfo($filename);
		$pathSegments = explode('/', $pathInfo['dirname'] ?? '');

		$exceptionDirectoryName = array_pop($pathSegments);

		if ($exceptionDirectoryName !== $this->exceptionsDirectoryName) {
			$file->addError(sprintf(
				'Exception file "%s" must be placed in "%s" directory (is in "%s").',
				$pathInfo['basename'],
				$this->exceptionsDirectoryName,
				$exceptionDirectoryName,
			), $classPointer, self::CODE_INCORRECT_EXCEPTION_DIRECTORY);
		}
	}


	private function checkThatExceptionIsChainable(PHP_CodeSniffer\Files\File $file, int $classPointer): void
	{
		$constructorPointer = $this->findConstructorMethodPointer($file, $classPointer);
		if ($constructorPointer === null) {
			return;
		}

		$typeHints = FunctionHelper::getParametersTypeHints($file, $constructorPointer);
		if (count($typeHints) === 0) {
			$file->addError(
				'Exception is not chainable. It must have optional \Throwable as last constructor argument.',
				$constructorPointer,
				self::CODE_NOT_CHAINABLE,
			);
			return;
		}
		$lastArgument = array_pop($typeHints);

		if ($lastArgument === null) {
			$file->addError(
				'Exception is not chainable. It must have optional \Throwable as last constructor argument and has none.',
				$constructorPointer,
				self::CODE_NOT_CHAINABLE,
			);
			return;
		}

		$lastArgumentTypeHint = ltrim($lastArgument->getTypeHint(), '?');
		if (str_ends_with(strtolower($lastArgumentTypeHint), '|null')) {
			$lastArgumentTypeHint = substr($lastArgumentTypeHint, 0, -5);
		}

		if (
			$lastArgumentTypeHint !== '\Throwable'
			&& !str_ends_with($lastArgumentTypeHint, 'Exception')
			&& !str_ends_with($lastArgumentTypeHint, 'Error')
		) {
			$file->addError(sprintf(
				'Exception is not chainable. It must have optional \Throwable as last constructor argument and has "%s".',
				$lastArgument->getTypeHint(),
			), $constructorPointer, self::CODE_NOT_CHAINABLE);
			return;
		}
	}


	private function findConstructorMethodPointer(PHP_CodeSniffer\Files\File $file, int $classPointer): int|null
	{
		$functionPointerToScan = $classPointer;
		while (($functionPointer = TokenHelper::findNext($file, T_FUNCTION, $functionPointerToScan)) !== null) {
			$functionName = FunctionHelper::getName($file, $functionPointer);
			if ($functionName === '__construct') {
				return $functionPointer;
			}
			$functionPointerToScan = $functionPointer + 1;
		}

		return null;
	}

}
