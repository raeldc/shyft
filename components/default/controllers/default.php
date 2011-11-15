<?php

class ComDefaultControllerDefault extends KControllerService
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		if($config->persistable && $this->isDispatched()) {
			$this->addBehavior('persistable');
		}
	}
	
	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	/* 
         * Disable controller persistency on non-HTTP requests, e.g. AJAX, and requests containing 
         * the tmpl variable set to component, e.g. requests using modal boxes. This avoids 
         * changing the model state session variable of the requested model, which is often 
         * undesirable under these circumstances. 
         */  
        
        $config->append(array(
    		'persistable'  => (KRequest::type() == 'HTTP'),
        ));

        // Add the manageable behavior only in admin mode and if controller is dispatched
        if ($config->dispatched && $config->request->mode == 'admin') 
        {
        	$config->append(array(
        		'behaviors' => array('manageable'),
        		'toolbars'  => array('menubar', $this->getIdentifier()->name),
	        ));

	        if($config->request->com == 'pages' || $config->request->page) 
	        {
	        	$config->append(array(
	        		'toolbars' => array('pages')
		        ));
	        }
        }

        parent::_initialize($config);
    }

	/**
	 * Returns an array with the redirect url, the message and the message type
	 *
	 * @return array	Named array containing url, message and messageType, or null if no redirect was set
	 */
	public function getRedirect()
	{
		$result = array();

		if(!empty($this->_redirect))
		{
			$result = array(
				'url' 		=> $this->_redirect,
				'message' 	=> $this->_redirect_message,
				'type' 		=> $this->_redirect_type,
			);
		}

		return $result;
	}

}
