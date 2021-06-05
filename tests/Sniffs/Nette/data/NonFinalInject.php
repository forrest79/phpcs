<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Methods;

class NonFinalInject extends Presenter
{

	final public function injectBase1(): void
	{
	}


	public function injectBase2(): void
	{
	}

}
