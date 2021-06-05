<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Exceptions;

interface InterfaceThatExtendsException extends \Consistence\Sniffs\Exceptions\InterfaceThatDoesNotExtendAnythingException
{

	public function extraString(): string;

}
