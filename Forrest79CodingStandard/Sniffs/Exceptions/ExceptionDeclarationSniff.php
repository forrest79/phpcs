<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Exceptions;

use PHP_CodeSniffer;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

/**
 * @author https://github.com/consistence/coding-standard
 */
final class ExceptionDeclarationSniff implements PHP_CodeSniffer\Sniffs\Sniff
{
	public const string CODE_NOT_ENDING_WITH_EXCEPTION = 'NotEndingWithException';
	public const string CODE_NOT_CHAINABLE = 'NotChainable';
	public const string CODE_INCORRECT_EXCEPTION_DIRECTORY = 'IncorrectExceptionDirectory';

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


	public function process(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		$extendedClassName = $phpcsFile->findExtendedClassName($stackPtr);
		if ($extendedClassName === false) {
			return; //does not extend anything
		}

		if (!str_ends_with($extendedClassName, 'Exception')) {
			return; // does not extend Exception, is not an exception
		}

		$this->checkExceptionName($phpcsFile, $stackPtr);

		$this->checkExceptionDirectoryName($phpcsFile, $stackPtr);

		$this->checkThatExceptionIsChainable($phpcsFile, $stackPtr);
	}


	private function checkExceptionName(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		$className = ClassHelper::getName($phpcsFile, $stackPtr);
		if (!str_ends_with($className, 'Exception')) {
			$phpcsFile->addError(sprintf(
				'Exception class name "%s" must end with "Exception".',
				$className,
			), $stackPtr, self::CODE_NOT_ENDING_WITH_EXCEPTION);
		}
	}


	private function checkExceptionDirectoryName(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		$filename = $phpcsFile->getFilename();

		// normalize path for Windows (PHP_CodeSniffer detects it with backslashes on Windows)
		$filename = str_replace('\\', '/', $filename);

		$pathInfo = pathinfo($filename);
		$pathSegments = explode('/', $pathInfo['dirname'] ?? '');

		$exceptionDirectoryName = array_pop($pathSegments);

		if ($exceptionDirectoryName !== $this->exceptionsDirectoryName) {
			$phpcsFile->addError(sprintf(
				'Exception file "%s" must be placed in "%s" directory (is in "%s").',
				$pathInfo['basename'],
				$this->exceptionsDirectoryName,
				$exceptionDirectoryName,
			), $stackPtr, self::CODE_INCORRECT_EXCEPTION_DIRECTORY);
		}
	}


	private function checkThatExceptionIsChainable(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		$constructorPointer = $this->findConstructorMethodPointer($phpcsFile, $stackPtr);
		if ($constructorPointer === null) {
			return;
		}

		$typeHints = FunctionHelper::getParametersTypeHints($phpcsFile, $constructorPointer);
		if (count($typeHints) === 0) {
			$phpcsFile->addError(
				'Exception is not chainable. It must have optional \Throwable as last constructor argument.',
				$constructorPointer,
				self::CODE_NOT_CHAINABLE,
			);
			return;
		}
		$lastArgument = array_pop($typeHints);

		if ($lastArgument === null) {
			$phpcsFile->addError(
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
			$phpcsFile->addError(sprintf(
				'Exception is not chainable. It must have optional \Throwable as last constructor argument and has "%s".',
				$lastArgument->getTypeHint(),
			), $constructorPointer, self::CODE_NOT_CHAINABLE);
		}
	}


	private function findConstructorMethodPointer(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): int|null
	{
		$functionPointerToScan = $stackPtr;
		while (($functionPointer = TokenHelper::findNext($phpcsFile, T_FUNCTION, $functionPointerToScan)) !== null) {
			$functionName = FunctionHelper::getName($phpcsFile, $functionPointer);
			if ($functionName === '__construct') {
				return $functionPointer;
			}
			$functionPointerToScan = $functionPointer + 1;
		}

		return null;
	}

}
