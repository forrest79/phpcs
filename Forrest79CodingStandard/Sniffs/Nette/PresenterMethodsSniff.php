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


	public function process(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		if ($this->isRegistered($phpcsFile, $stackPtr) && $this->isPublic($phpcsFile, $stackPtr)) {
			$phpcsFile->addError(sprintf('Method "%s" is not allowed to be public', $phpcsFile->getDeclarationName($stackPtr)), $stackPtr, self::CODE_NOT_PROTECTED);
		}
	}


	private function isPublic(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): bool
	{
		['scope' => $scope] = $phpcsFile->getMethodProperties($stackPtr);
		return $scope === 'public';
	}


	private function isRegistered(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): bool
	{
		return preg_match(sprintf('~^(%s)$~', implode('|', self::METHODS)), $phpcsFile->getDeclarationName($stackPtr)) === 1;
	}

}
