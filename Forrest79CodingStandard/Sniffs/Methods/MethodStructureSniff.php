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
	public $checkFiles = [];


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
			if (
				$tokens[$stackPointer - 2]['code'] === T_PUBLIC
				&& $tokens[$stackPointer + 2]['content'] !== '__construct'
			) {
				$methods[] = $tokens[$stackPointer + 2]['content'];
			}
		}

		$incorrect = array_diff_assoc(self::sort($methods), $methods);

		foreach ($incorrect as $method) {
			$phpcsFile->addError(sprintf('Method "%s" is not in correct order.', $method), 0, self::CODE_ALPHABETICAL_ORDER);
		}
	}


	/**
	 * @param array<string> $methods
	 * @return array<string>
	 */
	private static function sort(array $methods): array
	{
		sort($methods, SORT_STRING);
		return $methods;
	}

}
