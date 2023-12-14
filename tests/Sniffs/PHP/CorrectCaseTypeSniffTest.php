<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\PHP;

require __DIR__ . '/../../autoload.php';

use Forrest79CodingStandard\Sniffs;

/**
 * @testCase
 */
final class CorrectCaseTypeSniffTest extends Sniffs\TestCase
{

	public function testCorrectTypes(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/CorrectTypes.php');

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testIncorrectTypes(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/IncorrectTypes.php');

		$this->assertSniffError(
			$resultFile,
			9,
			'PropertyTypeFound',
			'PHP property type declarations must be uppercase; expected "NULL" but found "null"',
		);

		$this->assertSniffError(
			$resultFile,
			12,
			'ParamTypeFound',
			'PHP parameter type declarations must be uppercase; expected "NULL" but found "NUll"',
		);

		$this->assertSniffError(
			$resultFile,
			12,
			'ReturnTypeFound',
			'PHP return type declarations must be uppercase; expected "NULL" but found "null"',
		);
	}

}

(new CorrectCaseTypeSniffTest())->run();
