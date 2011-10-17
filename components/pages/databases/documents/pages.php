<?php

class ComPagesDatabaseDocumentPages extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->identity_column = 'slug';
	
		parent::_initialize($config);
	}
}