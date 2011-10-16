<?php

class ComApplicationControllerBehaviorPageable extends KControllerBehaviorAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'priority' => KCommand::PRIORITY_LOW
		));

		parent::_initialize($config);
	}

    public function _beforeDispatch(KCommandContext $context)
    {
    	$request = $this->getService('com://site/application.router')->getRequest();

    	// If in admin and component is pages or page is set.
    	if ($request['mode'] != 'site' && ($request->com == 'pages' || !empty($request->page))) 
    	{
    		// Get the manage tree for pages, assign it to left theme position
	        $this->getService('theme.container')->append('left',
	            $this->getService('com://site/pages.controller.page')
	                ->view('pages')
			        ->layout('manage_tree')
			        ->display()
	        );
    	}
    }
}