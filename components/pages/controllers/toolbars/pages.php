<?php

class ComPagesControllerToolbarPages extends KControllerToolbarDefault
{
    public function _commandNew(KControllerToolbarCommand $command)
    {
    	$command->append(array(
            'attribs' => array(
                'data-controls-modal' => 'page-content-types',
                'data-backdrop'	=> 'static',
            )
        ));
    }

    public function _commandNewgroup(KControllerToolbarCommand $command)
    {
    	$command->append(array(
            'attribs' => array(
            	'href' => $this->_controller->getView()->createRoute('page=&view=group&layout=form'),
            ),
        ));
    }

    public function getCommands()
    {
    	$this->reset()
    		->addNew()
    		->addNewgroup();

    	return parent::getCommands();
    }
}