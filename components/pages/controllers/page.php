<?php

class ComPagesControllerPage extends ComDefaultControllerDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'behaviors' => array('manageable'),
		));
		
		parent::_initialize($config);
	}
	
}