<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Methods;

use PHP_CodeSniffer;

/**
 * @author https://github.com/klapuch
 */
final class MethodStructureSniff implements PHP_CodeSniffer\Sniffs\Sniff
{
	public const CODE_ALPHABETICAL_ORDER = 'AlphabeticalOrder';

	/** @var array<string> */
	public array $checkFiles = [];


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
		foreach ($this->checkFiles as $file) {
			if (substr($phpcsFile->getFilename(), -strlen($file)) === $file) {
				$this->checkFile($phpcsFile, $stackPointer);
				return;
			}
		}
	}


	private function checkFile(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer): void
	{
		$methods = [];

		$tokens = $phpcsFile->getTokens();

		while (($stackPointer = $phpcsFile->findNext([T_FUNCTION], $stackPointer + 1)) !== FALSE) {
			assert(is_array($tokens[$stackPointer - 2]) && is_array($tokens[$stackPointer + 2]));
			if (
				$tokens[$stackPointer - 2]['code'] === T_PUBLIC
				&& $tokens[$stackPointer + 2]['content'] !== '__construct'
			) {
				assert(is_string($tokens[$stackPointer + 2]['content']));
				$methods[] = $tokens[$stackPointer + 2]['content'];
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
