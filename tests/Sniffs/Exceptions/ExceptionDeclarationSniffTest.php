<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Exceptions;

require __DIR__ . '/../../autoload.php';

use Forrest79CodingStandard\Sniffs;

/**
 * @testCase
 */
final class ExceptionDeclarationSniffTest extends Sniffs\TestCase
{

	public function testInvalidExceptionName(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/InvalidExceptionName.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertSniffError(
			$resultFile,
			5,
			ExceptionDeclarationSniff::CODE_NOT_ENDING_WITH_EXCEPTION,
			'Exception class name "InvalidExceptionName" must end with "Exception".',
		);
	}


	public function testValidClassName(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ValidNameException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testValidClassNameThatExtendsCustomException(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ValidClassNameThatExtendsCustomException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testAbstractExceptionWithValidNameException(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/AbstractExceptionWithValidNameException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testAbstractClassWithInvalidExceptionName(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/AbstractExceptionWithInvalidName.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertSniffError(
			$resultFile,
			5,
			ExceptionDeclarationSniff::CODE_NOT_ENDING_WITH_EXCEPTION,
			'Exception class name "AbstractExceptionWithInvalidName" must end with "Exception".',
		);
	}


	public function testClassThatDoesNotExtendAnything(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ClassThatDoesNotExtendAnything.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testClassThatExtendsRegularClass(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ClassThatDoesNotExtendException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testInterfaceThatDoesNotExtendAnything(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/InterfaceThatDoesNotExtendAnything.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testInterfaceThatDoesNotExtendAnythingException(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/InterfaceThatDoesNotExtendAnythingException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testInterfaceThatExtendsException(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/InterfaceThatExtendsException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testInterfaceThatExtendsExceptionIncorrectName(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/InterfaceThatExtendsExceptionIncorrectName.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertSniffError(
			$resultFile,
			5,
			ExceptionDeclarationSniff::CODE_NOT_ENDING_WITH_EXCEPTION,
			'Exception class name "InterfaceThatExtendsExceptionIncorrectName" must end with "Exception".',
		);
	}


	public function testExceptionWithConstructorWithoutParametersIsNotChainable(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ConstructWithoutParametersException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertSniffError(
			$resultFile,
			8,
			ExceptionDeclarationSniff::CODE_NOT_CHAINABLE,
			'Exception is not chainable. It must have optional \Throwable as last constructor argument.',
		);
	}


	public function testExceptionWithChainableConstructorIsChainable(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ChainableConstructorException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testExceptionWithCustomExceptionArgumentIsChainable(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/CustomExceptionArgumentChainableConstructorException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testExceptionWithErrorArgumentIsChainable(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ErrorArgumentChainableConstructorException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testExceptionWithNonchainableConstructorIsNotChainable(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/NonChainableConstructorException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertSniffError(
			$resultFile,
			8,
			ExceptionDeclarationSniff::CODE_NOT_CHAINABLE,
			'Exception is not chainable. It must have optional \Throwable as last constructor argument and has "string".',
		);
	}


	public function testExceptionWithConstructorWithoutParameterTypeHintIsNotChainable(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/NonChainableConstructorWithoutParameterTypehintException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertSniffError(
			$resultFile,
			8,
			ExceptionDeclarationSniff::CODE_NOT_CHAINABLE,
			'Exception is not chainable. It must have optional \Throwable as last constructor argument and has none.',
		);
	}


	public function testExceptionIsPlacedInCorrectDirectory(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ValidNameException.php', [
			'exceptionsDirectoryName' => ['value' => 'data', 'scope' => 'standard'],
		]);

		$this->assertNoSniffErrorInFile($resultFile);
	}


	public function testExceptionIsPlacedInIncorrectDirectory(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ValidNameException.php', [
			'exceptionsDirectoryName' => ['value' => 'exceptions', 'scope' => 'standard'],
		]);

		$this->assertSniffError(
			$resultFile,
			5,
			ExceptionDeclarationSniff::CODE_INCORRECT_EXCEPTION_DIRECTORY,
			'Exception file "ValidNameException.php" must be placed in "exceptions" directory (is in "data").',
		);
	}


	public function testExceptionIsPlacedInIncorrectDirectoryCaseSensitively(): void
	{
		$resultFile = $this->checkFile(__DIR__ . '/data/ValidNameException.php', [
			'exceptionsDirectoryName' => ['value' => 'Data', 'scope' => 'standard'],
		]);

		$this->assertSniffError(
			$resultFile,
			5,
			ExceptionDeclarationSniff::CODE_INCORRECT_EXCEPTION_DIRECTORY,
			'Exception file "ValidNameException.php" must be placed in "Data" directory (is in "data").',
		);
	}

}

(new ExceptionDeclarationSniffTest())->run();
