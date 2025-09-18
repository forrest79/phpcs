<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Methods;

use PHP_CodeSniffer;

/**
 * @author https://github.com/klapuch
 */
final class MethodStructureSniff implements PHP_CodeSniffer\Sniffs\Sniff
{
	public const CODE_ALPHABETICAL_ORDER = 'AlphabeticalOrder';

	/** @var list<string> */
	public array $checkFiles = [];


	/**
	 * @inheritDoc
	 */
	public function register(): array
	{
		return [T_CLASS];
	}


	public function process(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		foreach ($this->checkFiles as $file) {
			if (str_ends_with($phpcsFile->getFilename(), $file)) {
				$this->checkFile($phpcsFile, $stackPtr);
				return;
			}
		}
	}


	private function checkFile(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPtr): void
	{
		$methods = [];

		$tokens = $phpcsFile->getTokens();

		while (($stackPtr = $phpcsFile->findNext([T_FUNCTION], $stackPtr + 1)) !== false) {
			assert(is_array($tokens[$stackPtr - 2]) && is_array($tokens[$stackPtr + 2]));

			if (
				$tokens[$stackPtr - 2]['code'] === T_PUBLIC
				&& $tokens[$stackPtr + 2]['content'] !== '__construct'
			) {
				assert(is_string($tokens[$stackPtr + 2]['content']));
				$methods[] = $tokens[$stackPtr + 2]['content'];
			}
		}

		$incorrect = array_diff_assoc(self::sort($methods), $methods);

		foreach ($incorrect as $method) {
			$phpcsFile->addError(sprintf('Method "%s" is not in correct order.', $method), 0, self::CODE_ALPHABETICAL_ORDER);
		}
	}


	/**
	 * @param list<string> $methods
	 * @return list<string>
	 */
	private static function sort(array $methods): array
	{
		sort($methods, SORT_STRING);
		return $methods;
	}

}
