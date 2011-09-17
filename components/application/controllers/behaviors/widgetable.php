<?php

class ComApplicationControllerBehaviorWidgetable extends KControllerBehaviorAbstract
{
    protected function _afterDispatch(KCommandContext $context)
    {
        if ($context->application->isThemable()) 
        {
            
        }
    }
}