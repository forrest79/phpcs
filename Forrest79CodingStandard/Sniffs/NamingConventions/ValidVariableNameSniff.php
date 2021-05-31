<?php declare(strict_types=1);

namespace Consistence\Sniffs\NamingConventions;

use PHP_CodeSniffer;

final class ValidVariableNameSniff extends PHP_CodeSniffer\Sniffs\AbstractVariableSniff
{
	public const CODE_CAMEL_CAPS = 'NotCamelCaps';

	/** @var string[] */
	private static array $phpReservedVariables = [
		'_SERVER',
		'_GET',
		'_POST',
		'_REQUEST',
		'_SESSION',
		'_ENV',
		'_COOKIE',
		'_FILES',
		'GLOBALS',
	];


	/**
	 * @param int $stackPointer position of the double quoted string
	 */
	protected function processVariable(PHP_CodeSniffer\Files\File $file, $stackPointer): void
	{
		$tokens = $file->getTokens();
		$varName = ltrim($tokens[$stackPointer]['content'], '$');

		if (in_array($varName, self::$phpReservedVariables, true)) {
			return; // skip PHP reserved vars
		}

		$objOperator = $file->findPrevious([T_WHITESPACE], ($stackPointer - 1), null, true);
		if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
			return; // skip MyClass::$variable, there might be no control over the declaration
		}

		if (!PHP_CodeSniffer\Util\Common::isCamelCaps($varName, false, true, false)) {
			$error = 'Variable "%s" is not in valid camel caps format';
			$data = [$varName];
			$file->addError($error, $stackPointer, self::CODE_CAMEL_CAPS, $data);
		}
	}


	/**
	 * @param int $stackPointer position of the double quoted string
	 */
	protected function processMemberVar(PHP_CodeSniffer\Files\File $file, $stackPointer): void
	{
		// handled by PSR2.Classes.PropertyDeclaration
	}


	/**
	 * @param int $stackPointer position of the double quoted string
	 */
	protected function processVariableInString(PHP_CodeSniffer\Files\File $file, $stackPointer): void
	{
		// Consistence standard does not allow variables in strings, handled by Squiz.Strings.DoubleQuoteUsage
	}

}
