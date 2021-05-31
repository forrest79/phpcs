<?php declare(strict_types=1);

namespace Tools\PmgStandard\Sniffs\Methods;

final class ProtectedNetteSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	private const METHODS = ['beforeRender', 'startup', 'createComponent[A-Z].+'];


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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPointer): void
	{
		if ($this->isRegistered($phpcsFile, $stackPointer) && $this->isPublic($phpcsFile, $stackPointer)) {
			$phpcsFile->addError(sprintf('Method "%s" is not allowed to be public', $phpcsFile->getDeclarationName($stackPointer)), $stackPointer, 'NotProtected');
		}
	}


	private function isPublic(\PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer): bool
	{
		['scope' => $scope] = $phpcsFile->getMethodProperties($stackPointer);
		return $scope === 'public';
	}


	private function isRegistered(\PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer): bool
	{
		return preg_match(sprintf('~^(%s)$~', implode('|', self::METHODS)), $phpcsFile->getDeclarationName($stackPointer)) === 1;
	}

}
