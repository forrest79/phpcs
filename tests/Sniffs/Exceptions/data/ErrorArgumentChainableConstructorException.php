<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Exceptions;

class ErrorArgumentChainableConstructorException extends \Exception
{

	public function __construct(string $foo, \TypeError $e)
	{
		parent::__construct($foo, 0, $e);
	}

}
