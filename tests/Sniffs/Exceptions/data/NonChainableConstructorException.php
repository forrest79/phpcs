<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Exceptions;

class NonChainableConstructorException extends \Exception
{

	public function __construct(string $foo)
	{
		parent::__construct($foo, 0, null);
	}

}
