<?php

class ComContentDatabaseDocumentTypes extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->identity_column = 'type';
		$config->name = 'content_types';
		
		parent::_initialize($config);
	}
}