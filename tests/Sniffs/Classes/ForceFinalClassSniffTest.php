<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Classes;

require __DIR__ . '/../../autoload.php';

use Forrest79CodingStandard\Sniffs;

final class ForceFinalClassSniffTest extends Sniffs\TestCase
{

	public function testAbstactClass(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/AbstractClass.php');

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testFinalClass(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/FinalClass.php');

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testNotFinalClass(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/NotFinalClass.php');

		$this->assertSniffError(
			$resultFile,
			5,
			ForceFinalClassSniff::CODE_MISSING_FINAL,
			'All classes which are not extended must be final.'
		);
	}

}

(new ForceFinalClassSniffTest())->run();
