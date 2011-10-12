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
    	$request = $this->getService('com://site/application.router')->getContext()->component;
    	$context->caller->setRequest($request);

    	$component = clone $context->caller->getIdentifier();
    	$component->package = $request->com;

    	$context->caller->setComponent($request->com);
    }
}