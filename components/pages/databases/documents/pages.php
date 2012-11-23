<?php

class ComPagesDatabaseDocumentPages extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->name = 'pages';
		parent::_initialize($config);
	}
}