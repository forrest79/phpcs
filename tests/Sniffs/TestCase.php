<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs;

use PHP_CodeSniffer;
use Tester;

abstract class TestCase extends Tester\TestCase
{

	public function run(): void
	{
		Tester\Environment::setup();

		if (!defined('PHP_CODESNIFFER_CBF')) {
			define('PHP_CODESNIFFER_CBF', FALSE);
		}

		parent::run();
	}


	/**
	 * @param array<mixed> $sniffProperties
	 */
	protected function checkFile(string $filePath, array $sniffProperties = []): PHP_CodeSniffer\Files\File
	{
		if (!is_readable($filePath)) {
			throw new \Exception(sprintf('File "%s" is not readable', $filePath));
		}

		$codeSniffer = new PHP_CodeSniffer\Runner();
		$codeSniffer->config = new PHP_CodeSniffer\Config([
			'-s', // showSources must be on, so that errors are recorded
			'--standard=' . realpath(__DIR__ . '/../../Forrest79CodingStandard/ruleset.xml'), // use our standard
		]);

		$codeSniffer->init();

		if (count($sniffProperties) > 0) {
			$codeSniffer->ruleset->ruleset[$this->getSniffName()]['properties'] = $sniffProperties;
		}

		$codeSniffer->ruleset->sniffs = [$this->getSniffClassName() => new class implements PHP_CodeSniffer\Sniffs\Sniff {

			/**
			 * @inheritDoc
			 */
			public function register()
			{
				return [T_CLASS];
			}


			/**
			 * @inheritDoc
			 */
			public function process(PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
			{
			}

		}];
		$codeSniffer->ruleset->populateTokenListeners();

		$file = new PHP_CodeSniffer\Files\LocalFile($filePath, $codeSniffer->ruleset, $codeSniffer->config);
		$file->process();

		return $file;
	}


	protected function getSniffName(): string
	{
		return (string) preg_replace(
			[
				'~\\\~',
				'~\.Sniffs~',
				'~Sniff$~',
			],
			[
				'.',
				'',
				'',
			],
			$this->getSniffClassName(),
		);
	}


	protected function getSniffClassName(): string
	{
		return substr(get_class($this), 0, -strlen('Test'));
	}


	protected function assertSniffError(
		PHP_CodeSniffer\Files\File $resultFile,
		int $line,
		string $code,
		string|NULL $message = NULL,
	): void
	{
		$errors = $resultFile->getErrors();

		Tester\Assert::true(
			isset($errors[$line]),
			sprintf('Expected error on line %s, but none occurred', $line),
		);

		$expectedCode = $this->getSniffName() . '.' . $code;

		Tester\Assert::true(
			$this->hasError($errors[$line], $expectedCode, $message),
			sprintf(
				'Expected code %s%s, but not found on line %s.%sErrors found on this line:%s%s%s',
				$expectedCode,
				($message !== NULL) ? sprintf(' with message "%s"', $message) : '',
				$line,
				PHP_EOL,
				PHP_EOL,
				$this->getFormattedErrorsOnLine($errors, $line),
				PHP_EOL,
			),
		);
	}


	/**
	 * @param array<array<array{source: string, message: string}>> $errorsForLine
	 */
	private function hasError(iterable $errorsForLine, string $code, string|NULL $message = NULL): bool
	{
		foreach ($errorsForLine as $errorsForPosition) {
			foreach ($errorsForPosition as $error) {
				if (
					$error['source'] === $code
					&& ($message === NULL || strpos($error['message'], $message) !== FALSE)
				) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}


	protected function assertNoSniffError(PHP_CodeSniffer\Files\File $resultFile, int $line): void
	{
		$errors = $resultFile->getErrors();

		Tester\Assert::false(
			isset($errors[$line]),
			sprintf(
				'Expected no error on line %s, but errors found:%s%s%s',
				$line,
				PHP_EOL,
				$this->getFormattedErrorsOnLine($errors, $line),
				PHP_EOL,
			),
		);
	}


	protected function assertNoSniffErrorInFile(PHP_CodeSniffer\Files\File $file): void
	{
		$errorsForFile = $file->getErrors();

		Tester\Assert::same([], $errorsForFile, sprintf(
			'No errors expected, but %d errors found: %s%s%s%s',
			count($errorsForFile),
			PHP_EOL,
			PHP_EOL,
			$this->getFormattedErrorsForFile($errorsForFile),
			PHP_EOL,
		));
	}


	/**
	 * @param array<array<array<array{source: string, message: string}>>> $errorsForFile
	 */
	private function getFormattedErrorsForFile(array $errorsForFile): string
	{
		$message = '';
		foreach (array_keys($errorsForFile) as $line) {
			$message .= sprintf(
				'%d:%s%s%s',
				$line,
				PHP_EOL,
				$this->getFormattedErrorsOnLine($errorsForFile, $line),
				PHP_EOL,
			);
		}

		return $message;
	}


	/**
	 * @param array<array<array<array{source: string, message: string}>>> $errorsForFile
	 * @return string in format <source>: <message>
	 */
	private function getFormattedErrorsOnLine(array $errorsForFile, int $line): string
	{
		if (!isset($errorsForFile[$line])) {
			return '';
		}

		return implode(PHP_EOL, array_map(static function (array $errorsForPosition): string {
			return implode(PHP_EOL, array_map(static function (array $errorForPosition): string {
				return sprintf("\t" . '%s: %s', $errorForPosition['source'], $errorForPosition['message']);
			}, $errorsForPosition));
		}, $errorsForFile[$line]));
	}

}
