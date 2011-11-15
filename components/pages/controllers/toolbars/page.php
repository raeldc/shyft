<?php

class ComPagesControllerToolbarPage extends ComDefaultControllerToolbarDefault
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
            	'href' => $this->getController()->getView()->getRoute('page=&view=group&layout=form'),
            ),
        ));
    }
}