<?php

class ComPagesDatabaseDocumentPages extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'behaviors' => array('relatable'),
		));
	
		parent::_initialize($config);
	}
}