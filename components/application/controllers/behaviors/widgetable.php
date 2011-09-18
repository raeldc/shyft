<?php

class ComApplicationControllerBehaviorWidgetable extends KControllerBehaviorAbstract
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
    
        // Create the container identifier
        $identifier = 'com://'.$this->_identifier->application.'/application.template.container';
        KIdentifier::map('theme.container', $identifier);
    }
    
    protected function _beforeDispatch(KCommandContext $context)
    {
        if ($context->caller->isThemable()) 
        {
            // Make sure we inject the theme            
            $config = array(
                'view' => KFactory::get('theme'),
            );

            $container = KFactory::get('theme.container', $config);
            
            KFactory::get('theme')->setContainer($container);
        }
    }
}