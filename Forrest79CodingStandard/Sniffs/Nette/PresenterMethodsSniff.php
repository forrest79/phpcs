<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Nette;

use PHP_CodeSniffer;

/**
 * @author https://github.com/klapuch
 */
final class PresenterMethodsSniff implements PHP_CodeSniffer\Sniffs\Sniff
{
	public const CODE_NOT_PROTECTED = 'NotProtected';

	private const METHODS = [
		'beforeRender',
		'startup',
		'createComponent[A-Z].+',
	];


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
		if ($this->isRegistered($phpcsFile, $stackPointer) && $this->isPublic($phpcsFile, $stackPointer)) {
			$phpcsFile->addError(sprintf('Method "%s" is not allowed to be public', $phpcsFile->getDeclarationName($stackPointer)), $stackPointer, self::CODE_NOT_PROTECTED);
		}
	}


	private function isPublic(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer): bool
	{
		['scope' => $scope] = $phpcsFile->getMethodProperties($stackPointer);
		return $scope === 'public';
	}


	private function isRegistered(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer): bool
	{
		return preg_match(sprintf('~^(%s)$~', implode('|', self::METHODS)), (string) $phpcsFile->getDeclarationName($stackPointer)) === 1;
	}

}
