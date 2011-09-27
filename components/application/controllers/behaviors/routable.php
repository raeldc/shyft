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
    	$model = KFactory::get('com://site/pages.model.pages');
    	$page = $model->page(KRequest::get('get.page', 'cmd', 'default'))
    		->getItem();

    	if ($page->isNew()) {
    		// TODO: redirect to 404 page not found
    	}

    	// Determine the type of content the page is trying to access
    	if ($page->type == 'page') 
    	{
    		// We use page because we simply want to display the content of the page
    		$context->caller->setComponent('pages');
    		$context->caller->setRequest(array('view' => 'page'));
    	}
    	else
    	{
    		$component = KIdentifier::identify($page->type);
    		$component->name = 'dispatcher';

    		$context->caller->setComponent($component);
    	}
    }
}