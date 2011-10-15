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
}