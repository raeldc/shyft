<?php
/*
 *	- Implement System Workflow
 *	- Initialize the Content Workflow
 *	- Redirect where necessary
 *  - Render the Template
 */
class ComApplicationDispatcher extends KControllerAbstract implements KObjectInstantiatable
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_component = $config->component;
    }
    
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
    		// TODO: Behavior can be configurable in database
    		'behaviors'				=> array('routable'),
            // These values, component and request will just be defaults. But routable behavior can override them based on url.
            'component'             => KRequest::get('get.com', 'cmd', 'content'),
            'request'               => KRequest::get('get', 'string'),
        ))->append(array(
            // Routable behavior can figure this out. This is just a default value.
            'request' 				=> array('format' => KRequest::format() ? KRequest::format() : 'html')
        ));

        if (KRequest::type() != 'AJAX' && $config->request->format == 'html')
        {
            // Use these behaviors only when not on AJAX
            $config->append(array(
                'behaviors' => array('themable', 'widgetable')
            ));
        }

		parent::_initialize($config);
	}

	/**
     * Force creation of a singleton
     *
     * @return ComApplicationDispatcher
     */
    public static function getInstance(KConfigInterface $config, KFactoryInterface $factory)
    { 
       // Check if an instance with this identifier already exists or not
        if (!$factory->has($config->identifier))
        {
            //Create the singleton
            $classname = $config->identifier->classname;
            $instance  = new $classname($config);
            $factory->set($config->identifier, $instance);
        }
        
        return $factory->get($config->identifier);
    }

	protected function _actionDispatch(KCommandContext $context)
	{
        $context->application = $this;
        return $this->getComponent()->execute('dispatch', $context);
	}

    /**
     * Method to get a Component Dispatcher object
     *
     * @return  KDispatcherAbstract
     */
    public function getComponent()
    {
        if(!($this->_component instanceof KDispatcherAbstract))
        {  
            //Make sure we have a dispatcher identifier
            if(!($this->_component instanceof KIdentifier)) {
                $this->setComponent($this->_component);
            }

            $this->_component = KFactory::get($this->_component);
        }
    
        return $this->_component;
    }

    /**
     * Method to set a component dispatcher object attached to the application dispatcher
     *
     * @param   mixed   An object that implements KObjectIdentifiable, an object that
     *                  implements KIdentifierInterface or valid identifier string
     * @throws  KDispatcherException    If the identifier is not a dispatcher identifier
     * @return  KDispatcherAbstract
     */
    public function setComponent($component)
    {
        if(!($component instanceof KDispatcherAbstract))
        {
            if(is_string($component) && strpos($component, '.') === false ) 
            {
                // Dispatcher names are always singular
                if(KInflector::isPlural($component)) {
                    $component = KInflector::singularize($component);
                } 
                
                $identifier             = clone $this->_identifier;
                $identifier->package    = $this->_component;
                $identifier->path       = array();
                $identifier->name       = 'dispatcher';
            }
            else $identifier = KIdentifier::identify($component);

            if($identifier->name != 'dispatcher') {
                throw new KDispatcherException('Identifier: '.$identifier.' is not a component dispatcher identifier');
            }

            $component = $identifier;
        }
        
        $this->_component = $component;
    
        return $this;
    }
}