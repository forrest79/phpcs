<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Nette;

require __DIR__ . '/../../autoload.php';

use Forrest79CodingStandard\Sniffs;

final class PresenterMethodsSniffTest extends Sniffs\TestCase
{

	public function testCorrectPresenterMethods(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/CorrectPresenterMethods.php');

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testIncorrectPresenterMethodsStartup(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/IncorrectPresenterMethodsStartup.php');

		$this->assertSniffError(
			$resultFile,
			8,
			PresenterMethodsSniff::CODE_NOT_PROTECTED,
			'Method "startup" is not allowed to be public',
		);
	}


	public function testIncorrectPresenterMethodsBeforeRender(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/IncorrectPresenterMethodsBeforeRender.php');

		$this->assertSniffError(
			$resultFile,
			13,
			PresenterMethodsSniff::CODE_NOT_PROTECTED,
			'Method "beforeRender" is not allowed to be public',
		);
	}


	public function testIncorrectPresenterMethodsCreateComponent(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/IncorrectPresenterMethodsCreateComponent.php');

		$this->assertSniffError(
			$resultFile,
			18,
			PresenterMethodsSniff::CODE_NOT_PROTECTED,
			'Method "createComponentTest" is not allowed to be public',
		);
	}

}

(new PresenterMethodsSniffTest())->run();
