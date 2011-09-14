<?php
/*
 *	- Implement System Workflow
 *	- Initialize the Content Workflow
 *	- Redirect where necessary
 *  - Render the Template
 */
class ComApplicationDispatcher extends KControllerAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
    		'request'				=> KRequest::get('get', 'string'),
    		// TODO: Behavior should be configurable in database
    		'behaviors'				=> array('routable'),
        ))->append(array(
            'request' 				=> array('format' => KRequest::format() ? KRequest::format() : 'html')
        ));
	
		parent::_initialize($config);
	}

	public static function getInstance($config = array())
    {
        static $instance;
        
        if ($instance === NULL) 
        {
            //Create the singleton
            $classname = $config->identifier->classname;
            $instance = new $classname($config);
              
            //Add the factory map to allow easy access to the singleton
            KIdentifier::map('application', $config->identifier);
        }
        
        return $instance;
    }
	
	public function _actionDispatch(KCommandContext $context)
	{
	
	}
}