<?php

class ComApplicationControllerBehaviorWidgetable extends KControllerBehaviorAbstract
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
    
        // Create the container identifier
        $identifier = 'com://'.$this->getIdentifier()->application.'/application.template.container';
        KService::setAlias('theme.container', $identifier);
    }
    
    protected function _beforeDispatch(KCommandContext $context)
    {
        if($context->caller->isThemable())
        {
            $container = $this->getService('theme.container', array(
                // Make sure we inject the theme
                'view' => $this->getService('theme'),
            ));

            // Set the container for the theme
            $this->getService('theme')->setContainer($container);
        }
    }

    protected function _afterDispatch(KCommandContext $context)
    {
        if($context->application->isThemable())
        {   
            // Inject the dispatcher's result into the 'page' container.
            // This assumes that widgetable's _afterDispatch is executed before themeable's
            $this->getService('theme.container')->append('page', $context->result);
        }
    }
}