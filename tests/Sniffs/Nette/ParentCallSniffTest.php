<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Nette;

require __DIR__ . '/../../autoload.php';

use Forrest79CodingStandard\Sniffs;

/**
 * @testCase
 */
final class ParentCallSniffTest extends Sniffs\TestCase
{

	public function testCorrectParentCall(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/CorrectParentCall.php');

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testIncorrectParentCall(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/IncorrectParentCall.php');

		$this->assertSniffError(
			$resultFile,
			8,
			ParentCallSniff::CODE_MISSING_PARENT,
			'All the methods (beforeRender) have to call parent::',
		);
	}

}

(new ParentCallSniffTest())->run();
