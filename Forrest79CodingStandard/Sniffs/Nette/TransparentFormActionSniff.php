<?php declare(strict_types=1);

namespace Tools\PmgStandard\Sniffs\Methods;

final class TransparentFormActionSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	private const PROHIBITED_METHODS = [
		'onSuccess',
		'onError',
		'onValidate',
		'onSubmit',
		'onClick',
	];


	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [T_OBJECT_OPERATOR];
	}


	/**
	 * @param int $stackPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$property = $tokens[$stackPointer + 1] ?? ['code' => NULL, 'content' => NULL];
		$objectThis = $tokens[$stackPointer + 8] ?? ['code' => NULL, 'content' => NULL];
		$flow = [
			($tokens[$stackPointer]['code'] ?? NULL) === T_OBJECT_OPERATOR, // ->
			$property['code'] === T_STRING, // onSuccess, onError, ..
			($tokens[$stackPointer + 2]['code'] ?? NULL) === 'PHPCS_T_OPEN_SQUARE_BRACKET', // [
			($tokens[$stackPointer + 3]['code'] ?? NULL) === 'PHPCS_T_CLOSE_SQUARE_BRACKET', // ]
			($tokens[$stackPointer + 4]['code'] ?? NULL) === T_WHITESPACE, // WHITE_SCAPE
			($tokens[$stackPointer + 5]['code'] ?? NULL) === 'PHPCS_T_EQUAL', // =
			($tokens[$stackPointer + 6]['code'] ?? NULL) === T_WHITESPACE, // WHITE_SPACE
			($tokens[$stackPointer + 7]['code'] ?? NULL) === 'PHPCS_T_OPEN_SHORT_ARRAY', // [
			($objectThis['code'] ?? NULL) === T_VARIABLE, // $this
		];
		if (array_filter($flow) === $flow && $objectThis['content'] === '$this' && in_array($property['content'], self::PROHIBITED_METHODS, TRUE)) {
			$phpcsFile->addError(sprintf('Using $form->%s[] = [$this, \'callback\'] is prohibited.', implode('|', self::PROHIBITED_METHODS)), $stackPointer, 'ProhibitedMethod');
		}
	}

}
