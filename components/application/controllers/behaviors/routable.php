<?php

class ComApplicationControllerBehaviorRoutable extends KControllerBehaviorAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'priority' => KCommand::PRIORITY_HIGHEST,
		));
	
		parent::_initialize($config);
	}
	
	protected function _beforeDispatch(KCommandContext $context)
    {
    	// Get the page model to get the current item
    	$page = KFactory::get('com://site/pages.model.page')
    		->page(KRequest::get('get.page', 'cmd', 'default'))
    		->getItem();

    	if ($page->isNew()) {
    		// TODO: redirect to 404 page not found
    	}

    	// Determine the type of content the page is trying to access and call it.
    	$component = KIdentifier::identify($page->type);
		$component->name = 'dispatcher';
		
		$context->caller->setRequest($page->parameters);
		$context->caller->setComponent($component);
    }
}