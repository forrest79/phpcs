<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Methods;

class IncorrectPresenterMethodsBeforeRender
{

	protected function startup(): void
	{
	}


	public function beforeRender(): void
	{
	}


	protected function createComponentTest(): void
	{
	}


	public function actionTest(): void
	{
	}

}
