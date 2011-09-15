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
    }
    
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
        }

        return $instance;
    }

    protected function _actionMap(KCommandContext $context)
    {
        KIdentifier::map('com:application.document', 'com://site/application.view.theme');
    }
	
	protected function _actionDispatch(KCommandContext $context)
	{
       return 'Solid Framework + Fast and Scalable Database + Content Workflow Management = The Web Developer&rsquo;s Dream!';
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
}