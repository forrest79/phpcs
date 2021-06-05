<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\NamingConventions;

require __DIR__ . '/../../autoload.php';

use Forrest79CodingStandard\Sniffs;
use PHP_CodeSniffer;

final class ValidVariableNameSniffTest extends Sniffs\TestCase
{

	public function testValidVariable(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 10);
	}


	public function testNotCamelCaps(): void
	{
		$this->assertSniffError($this->getFileReport(), 11, ValidVariableNameSniff::CODE_CAMEL_CAPS, 'incorrect_variable');
	}


	public function testVariableOnObject(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 12);
	}


	public function testVariableOnClass(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 13);
	}


	public function testPhpReservedVariables(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 14);
	}


	private function getFileReport(): PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(__DIR__ . '/data/FooClass.php');
	}

}

(new ValidVariableNameSniffTest())->run();
