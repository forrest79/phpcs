<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Nette;

use PHP_CodeSniffer;

/**
 * @author https://github.com/klapuch
 */
final class ParentCallSniff implements PHP_CodeSniffer\Sniffs\Sniff
{
	public const CODE_MISSING_PARENT = 'MissingParent';

	private const METHODS = ['beforeRender'];


	/**
	 * @inheritDoc
	 */
	public function register(): array
	{
		return [T_FUNCTION];
	}


	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer\Files\File $phpcsFile, $stackPointer): void
	{
		if ($this->isRegistered($phpcsFile, $stackPointer) && !$this->hasParentCall($phpcsFile, $stackPointer)) {
			$phpcsFile->addError(sprintf('All the methods (%s) have to call parent::', implode(', ', self::METHODS)), $stackPointer, self::CODE_MISSING_PARENT);
		}
	}


	private function isRegistered(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer): bool
	{
		return (bool) array_uintersect([$phpcsFile->getDeclarationName($stackPointer)], self::METHODS, 'strcasecmp');
	}


	private function hasParentCall(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		foreach (self::METHODS as $method) {
			$openBracket = $phpcsFile->findNext([T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET], $stackPointer);
			if ($openBracket === FALSE) {
				throw new \InvalidArgumentException('Can\'t find open bracket.');
			}

			$closeBracket = $tokens[$openBracket]['bracket_closer'];

			$bodyPointers = array_keys(array_slice($tokens, $openBracket, ($closeBracket - $openBracket) + 1, TRUE));

			foreach ($bodyPointers as $pointer) {
				if ($this->hasParentCallTokens($tokens, $pointer, $method)) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}


	/**
	 * @param array<int, array{code: string|int, content: string}> $tokens
	 */
	private function hasParentCallTokens(array $tokens, int $stackPointer, string $method): bool
	{
		return $tokens[$stackPointer]['code'] === T_PARENT
			&& $tokens[$stackPointer + 1]['code'] === T_DOUBLE_COLON
			&& $tokens[$stackPointer + 2]['code'] === T_STRING
			&& strcasecmp($tokens[$stackPointer + 2]['content'], $method) === 0;
	}

}
