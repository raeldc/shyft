<?php

class ComApplicationControllerBehaviorThemable extends KControllerBehaviorAbstract
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		// Map a shortcut to the theme
		KIdentifier::setAlias('theme', 'com://site/application.view.theme');
	}

    protected function _afterDispatch(KCommandContext $context)
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
            	// Wrap it around the template
                $context->result = KFactory::get('theme')
					->display();
            }
            else return $context->result;
        }
    }
}