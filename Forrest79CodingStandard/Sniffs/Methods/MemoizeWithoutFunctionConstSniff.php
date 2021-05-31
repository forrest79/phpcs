<?php declare(strict_types=1);

namespace Tools\PmgStandard\Sniffs\Methods;

final class MemoizeWithoutFunctionConstSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	/**
	 * @inheritDoc
	 */
	public function register(): array
	{
		return [T_VARIABLE];
	}


	/**
	 * @inheritDoc
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		if (
			$tokens[$stackPointer]['code'] === T_VARIABLE
			&& $tokens[$stackPointer]['content'] === '$this'
			&& $tokens[$stackPointer + 1]['code'] === T_OBJECT_OPERATOR
			&& $tokens[$stackPointer + 2]['code'] === T_STRING
			&& $tokens[$stackPointer + 2]['content'] === 'memoize'
			&& $tokens[$stackPointer + 3]['code'] === 'PHPCS_T_OPEN_PARENTHESIS'
			&& ((
				$tokens[$stackPointer + 4]['code'] === 'PHPCS_T_OPEN_SHORT_ARRAY'
				&& $tokens[$stackPointer + 5]['code'] === T_FUNC_C
				&& $tokens[$stackPointer + 5]['content'] === '__FUNCTION__'
			) || (
				$tokens[$stackPointer + 4]['code'] === T_FUNC_C
				&& $tokens[$stackPointer + 4]['content'] === '__FUNCTION__'
			))
		) {
			$phpcsFile->addError('Use __METHOD__ instead of __FUNCTION__ for memoize call', $stackPointer, 'FunctionToMethod');
		}
	}

}
