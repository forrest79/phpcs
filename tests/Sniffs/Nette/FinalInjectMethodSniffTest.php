<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Nette;

require __DIR__ . '/../../autoload.php';

use Forrest79CodingStandard\Sniffs;

/**
 * @testCase
 */
final class FinalInjectMethodSniffTest extends Sniffs\TestCase
{

	public function testFinalPresenter(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/FinalPresenter.php');

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testFinalInject(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/FinalInject.php');

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testNonFinalInject(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/NonFinalInject.php');

		$this->assertSniffError(
			$resultFile,
			13,
			FinalInjectMethodSniff::CODE_MISSING_FINAL,
			'Non final presenter class must have final inject methods',
		);
	}

}

(new FinalInjectMethodSniffTest())->run();
