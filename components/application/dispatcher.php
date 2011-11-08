<?php
/*
 *	- Implement System Workflow
 *	- Initialize the Content Workflow
 *	- Redirect where necessary
 *  - Render the Template
 */
class ComApplicationDispatcher extends KControllerAbstract implements KServiceInstantiatable
{
    protected $_router;
    protected $_component;

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // Empty the request because we are getting it from the router.
        $this->_request = null;
    }

	/**
     * Force creation of a singleton
     *
     * @return ComApplicationDispatcher
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

	protected function _actionDispatch(KCommandContext $context)
	{
        $context->application = $this;
        return $this->getComponent()->execute('dispatch', $context);
	}

    public function getRouter()
    {
        if(!($this->_router instanceof SRouterAbstract))
        {
            if(is_string($this->_router) && strpos($this->_router, '.') === true)
            {
                $identifier = $this->getIdentifier($this->_router);   
            }
            else $identifier = $this->getIdentifier('com://site/application.router');

            if($identifier->name != 'router') {
                throw new KDispatcherException('Identifier: '.$identifier.' is not a routeridentifier');
            }

            $this->_router = $this->getService($identifier);
        }

        return $this->_router;
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
            $request = $this->getRequest();

            // Get the component identifier
            $this->setComponent($request->com);

            $config = array(
                'request' => $request,
            );

            $this->_component = $this->getService($this->_component, $config);
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
                $identifier             = clone $this->getIdentifier();
                $identifier->package    = $component;
                $identifier->path       = array();
                $identifier->name       = 'dispatcher';
            }
            else $identifier = $this->getIdentifier($component);

            if($identifier->name != 'dispatcher') {
                throw new KDispatcherException('Identifier: '.$identifier.' is not a component dispatcher identifier');
            }

            $component = $identifier;
        }

        $this->_component = $component;

        return $this;
    }

    /**
     * Get the request information from the Router
     *
     * @param array An associative array of request information
     * @return KControllerBread
     */
    public function getRequest()
    {
        if(!($this->_request instanceof KConfig)) {
            $this->_request = $this->getRouter()->getRequest();
        }

        return $this->_request;
    }
}