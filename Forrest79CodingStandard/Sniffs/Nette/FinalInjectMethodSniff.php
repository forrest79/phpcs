<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Nette;

use PHP_CodeSniffer;
use PHP_CodeSniffer\Util\Tokens;

/**
 * @author https://github.com/klapuch
 */
final class FinalInjectMethodSniff implements PHP_CodeSniffer\Sniffs\Sniff
{
	public const CODE_MISSING_FINAL = 'MissingFinal';

	/** @var array<bool> */
	private array $isFinalClass = [];


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
		if (!$this->isFinalClass($phpcsFile) && !$this->isFinalInjectMethod($phpcsFile, $stackPointer)) {
			$phpcsFile->addError('Non final presenter class must have final inject methods', $stackPointer, self::CODE_MISSING_FINAL);
		}
	}


	private function isFinalInjectMethod(PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		if (preg_match('~^inject[A-Z]{1}.+~', (string) $phpcsFile->getDeclarationName($stackPointer)) === 1) {
			$firstModifier = $phpcsFile->findPrevious(Tokens::$methodPrefixes, $stackPointer - 1);
			if ($firstModifier === FALSE) {
				throw new \InvalidArgumentException('Can\'t find first modifier.');
			}
			$secondModifier = $phpcsFile->findPrevious(Tokens::$methodPrefixes, $firstModifier - 1);

			assert(is_array($tokens[$firstModifier]) && is_array($tokens[$secondModifier]));
			return in_array(T_FINAL, [$tokens[$firstModifier]['code'], $tokens[$secondModifier]['code']], TRUE);
		}

		return TRUE;
	}


	private function isFinalClass(PHP_CodeSniffer\Files\File $phpcsFile): bool
	{
		if (!isset($this->isFinalClass[$phpcsFile->getFilename()])) {
			$tokens = $phpcsFile->getTokens();
			foreach ($tokens as $stackPointer => $token) {
				assert(is_array($token));

				if ($token['code'] === T_CLASS) {
					$previous = $phpcsFile->findPrevious(Tokens::$methodPrefixes, $stackPointer - 1);

					assert(is_array($tokens[$previous]));
					return $this->isFinalClass[$phpcsFile->getFilename()] = ($previous !== FALSE && $tokens[$previous]['code'] === T_FINAL);
				}
			}

			return $this->isFinalClass[$phpcsFile->getFilename()] = FALSE;
		}

		return $this->isFinalClass[$phpcsFile->getFilename()];
	}

}
