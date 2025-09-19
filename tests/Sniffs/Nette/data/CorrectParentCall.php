<?php declare(strict_types=1);

namespace Forrest79CodingStandard\Sniffs\Methods;

class CorrectParentCall extends ParentCall
{

	protected function beforeRender(): void
	{
		parent::beforeRender();
	}


	protected function afterRender(): void
	{
		parent::afterRender();
	}

}
