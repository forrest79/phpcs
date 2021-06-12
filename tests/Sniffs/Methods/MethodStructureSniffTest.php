<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Methods;

require __DIR__ . '/../../autoload.php';

use Forrest79CodingStandard\Sniffs;

final class MethodStructureSniffTest extends Sniffs\TestCase
{

	public function testCorrectOrder(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/CorrectAlphabeticalOrder.php', [
			'checkFiles' => ['/data/CorrectAlphabeticalOrder.php'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testIncorrectOrder(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/IncorrectAlphabeticalOrder.php', [
			'checkFiles' => ['/data/IncorrectAlphabeticalOrder.php'],
		]);

		$this->assertSniffError(
			$resultFile,
			1,
			MethodStructureSniff::CODE_ALPHABETICAL_ORDER
		);
	}

}

(new MethodStructureSniffTest())->run();
