<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer;

/**
 * @author https://github.com/klapuch
 */
final class ForceFinalClassSniff implements PHP_CodeSniffer\Sniffs\Sniff
{
	public const CODE_MISSING_FINAL = 'MissingFinal';


	/**
	 * @inheritDoc
	 */
	public function register(): array
	{
		return [T_CLASS];
	}


	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer\Files\File $phpcsFile, $stackPointer): void
	{
		if ($phpcsFile->findPrevious([T_FINAL, T_ABSTRACT], $stackPointer) === FALSE) {
			$fix = $phpcsFile->addFixableError('All classes which are not extended must be final.', $stackPointer, self::CODE_MISSING_FINAL);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($stackPointer, 'final class');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
