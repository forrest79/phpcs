<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\PHP;

class CorrectTypes
{
	private string $a;

	private float|NULL $b;


	public function fooMethod(string|NULL $param): int|NULL
	{
		return NULL;
	}

}
