<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\PHP;

class IncorrectTypes
{
	private string $a;

	private float|null $b;


	public function fooMethod(string|NUll $param): int|null
	{
		return NULL;
	}

}
