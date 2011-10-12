<?php

/*
 *	Instantiate All Singletons with configuration from the database
 *
*/
class ComApplicationControllerBehaviorConfigurable extends KControllerBehaviorAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'priority' => KCommand::PRIORITY_HIGHEST
		));

		parent::_initialize($config);
	}

	protected function _beforeDispatch(KCommandContext $context)
    {
    	
    }
}