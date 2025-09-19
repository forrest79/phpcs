<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer;

/**
 * @author https://github.com/consistence/coding-standard
 */
final class ValidVariableNameSniff extends PHP_CodeSniffer\Sniffs\AbstractVariableSniff
{
	public const string CODE_CAMEL_CAPS = 'NotCamelCaps';

	private const array PHP_RESERVED_VARIABLES = [
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


	protected function processVariable(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		$tokens = $phpcsFile->getTokens();
		assert(is_array($tokens[$stackPtr]) && is_string($tokens[$stackPtr]['content']));

		$varName = ltrim($tokens[$stackPtr]['content'], '$');

		if (in_array($varName, self::PHP_RESERVED_VARIABLES, true)) {
			return; // skip PHP reserved vars
		}

		$objOperator = $phpcsFile->findPrevious([T_WHITESPACE], ($stackPtr - 1), null, true);
		assert(is_int($objOperator) && is_array($tokens[$objOperator]));
		if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
			return; // skip MyClass::$variable, there might be no control over the declaration
		}

		if (!PHP_CodeSniffer\Util\Common::isCamelCaps($varName, false, true, false)) {
			$error = 'Variable "%s" is not in valid camel caps format';
			$data = [$varName];
			$phpcsFile->addError($error, $stackPtr, self::CODE_CAMEL_CAPS, $data);
		}
	}


	protected function processMemberVar(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		// handled by PSR2.Classes.PropertyDeclaration
	}


	protected function processVariableInString(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		// Consistence standard does not allow variables in strings, handled by Squiz.Strings.DoubleQuoteUsage
	}

}
