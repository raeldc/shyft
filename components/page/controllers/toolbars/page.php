<?php

class ComPageControllerToolbarPage extends KControllerToolbarDefault
{
    /**
     * Push the toolbar into the view
     *
     * @param   KEvent  A event object
     */
    public function onBeforeControllerGet(KEvent $event)
    {   
        $event->caller->getView()->toolbar = $this;
    }

    public function getCommands()
    {
    	// We don't want other toolbars to appear here
    	$this->reset()->addSave();

    	return parent::getCommands();
    }

    public function _commandSave(KControllerToolbarCommand $command)
    {
    	// Force the toolbar to be an apply action
    	$command->append(array(
    		'label' => 'Save',
            'attribs' => array(
                'data-action' => 'apply',
            )
        ));
    }
}