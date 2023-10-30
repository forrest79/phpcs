<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\PHP;

use PHP_CodeSniffer;

final class CorrectCaseTypeSniff extends PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseTypeSniff
{

	/**
	 * @param int $stackPtr
	 * @param string $type
	 * @param string $error
	 * @param string $errorCode
	 */
	protected function processType(PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr, $type, $error, $errorCode): void
	{
		if (in_array(strtolower($type), ['null', 'true', 'false'], TRUE)) {
			$error = str_replace('lowercase', 'uppercase', $error);
			$typeCorrect = strtoupper($type);
			$typeOpposite = strtolower($type);
			$typeCase = 'upper';
			$typeCaseOpposite = 'lower';
		} else {
			$typeCorrect = strtolower($type);
			$typeOpposite = strtoupper($type);
			$typeCase = 'lower';
			$typeCaseOpposite = 'upper';
		}

		if ($typeCorrect === $type) {
			$phpcsFile->recordMetric($stackPtr, 'PHP type case', $typeCase);
			return;
		}

		if ($type === $typeOpposite) {
			$phpcsFile->recordMetric($stackPtr, 'PHP type case', $typeCaseOpposite);
		} else {
			$phpcsFile->recordMetric($stackPtr, 'PHP type case', 'mixed');
		}

		$data = [
			$typeCorrect,
			$type,
		];

		$fix = $phpcsFile->addFixableError($error, $stackPtr, $errorCode, $data);
		if ($fix === TRUE) {
			$phpcsFile->fixer->replaceToken($stackPtr, $typeCorrect);
		}
	}

}
