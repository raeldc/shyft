<?php

class ComApplicationControllerBehaviorRoutable extends KControllerBehaviorAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'priority' => KCommand::PRIORITY_HIGH
		));

		parent::_initialize($config);
	}

	protected function _beforeDispatch(KCommandContext $context)
    {

    }
}