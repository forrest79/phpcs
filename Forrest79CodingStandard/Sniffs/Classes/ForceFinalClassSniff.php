<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer;

final class ForceFinalClassSniff implements PHP_CodeSniffer\Sniffs\Sniff
{

	/**
	 * @return array<string>
	 */
	public function register(): array
	{
		return [T_CLASS];
	}


	/**
	 * @param int $stackPointer
	 */
	public function process(PHP_CodeSniffer\Files\File $phpcsFile, $stackPointer): void
	{
		if ($phpcsFile->findPrevious([T_FINAL, T_ABSTRACT], $stackPointer) === FALSE) {
			$fix = $phpcsFile->addFixableError('All classes which are not extended must be final.', $stackPointer, 'MissingFinal');
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($stackPointer, 'final class');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
