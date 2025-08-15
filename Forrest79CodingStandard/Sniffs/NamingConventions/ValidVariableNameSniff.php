<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer;

/**
 * @author https://github.com/consistence/coding-standard
 */
final class ValidVariableNameSniff extends PHP_CodeSniffer\Sniffs\AbstractVariableSniff
{
	public const CODE_CAMEL_CAPS = 'NotCamelCaps';

	private const PHP_RESERVED_VARIABLES = [
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
	 * @inheritDoc
	 */
	protected function processVariable(PHP_CodeSniffer\Files\File $file, $stackPointer): void
	{
		$tokens = $file->getTokens();
		assert(is_array($tokens[$stackPointer]) && is_string($tokens[$stackPointer]['content']));

		$varName = ltrim($tokens[$stackPointer]['content'], '$');

		if (in_array($varName, self::PHP_RESERVED_VARIABLES, true)) {
			return; // skip PHP reserved vars
		}

		$objOperator = $file->findPrevious([T_WHITESPACE], ($stackPointer - 1), null, true);
		assert(is_int($objOperator) && is_array($tokens[$objOperator]));
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
	 * @inheritDoc
	 */
	protected function processMemberVar(PHP_CodeSniffer\Files\File $file, $stackPointer): void
	{
		// handled by PSR2.Classes.PropertyDeclaration
	}


	/**
	 * @inheritDoc
	 */
	protected function processVariableInString(PHP_CodeSniffer\Files\File $file, $stackPointer): void
	{
		// Consistence standard does not allow variables in strings, handled by Squiz.Strings.DoubleQuoteUsage
	}

}
