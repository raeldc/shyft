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

        $this->registerCallback('before.dispatch', array($this, 'map'));

        if(KRequest::method() != 'GET') {
            $this->registerCallback('after.dispatch' , array($this, 'forward'));
        }

        $this->registerCallback('after.dispatch', array($this, 'render'));

        $this->_component = $config->component;
    }
    
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
    		'request'				=> KRequest::get('get', 'string'),
    		// TODO: Behavior should be configurable in database
    		'behaviors'				=> array('routable'),
            'component'             => KRequest::get('get.com', 'cmd', 'contents'),
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
        }

        return $instance;
    }

    protected function _actionMap(KCommandContext $context)
    {
        KIdentifier::map('com:application.document', 'com://site/application.view.theme');
    }
	
	protected function _actionDispatch(KCommandContext $context)
	{
        return $this->getComponent()->execute('dispatch', $context);
	}

    protected function _actionForward(KCommandContext $context)
    {
        //TODO: Create redirect
    }

    protected function _actionRender(KCommandContext $context)
    {
        //Headers
        if($context->headers) 
        {
            foreach($context->headers as $name => $value) {
                header($name.' : '.$value);
            }
        }

        //Status
        if($context->status) {
           header(KHttpResponse::getHeader($context->status));
        }

        if (is_string($context->result)) 
        {
            // If Ajax, don't use the template
            if(KRequest::type() != 'AJAX')
            {
                return KFactory::get('com:application.document')
                    ->addtoContainer('page', $context->result)
                    ->display();
            }
            else return $context->result;
        }
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