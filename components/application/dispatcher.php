<?php
/*
 *	- Implement System Workflow
 *	- Initialize the Content Workflow
 *	- Redirect where necessary
 *  - Render the Template
 */
class ComApplicationDispatcher extends KControllerAbstract
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback('before.dispatch', array($this, 'prepare'));

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

    protected function _actionPrepare(KCommandContext $context)
    {
        // TODO: Change which view to use depending on format
        $document = KFactory::get('com://site/application.view.theme', array(
            // TODO: This is where we configure the theme
        ));

        KIdentifier::map('com://site/application.view.theme', 'com:application.document');

        // Let's save the document in the factory
        KFactory::set('com:application.document', $document);
    }
	
	protected function _actionDispatch(KCommandContext $context)
	{
        return 'Flowku - Content Workflow System';
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
                    ->setResult($context->result)
                    ->display();
            }
            else return $context->result;
        }
        
    }
}