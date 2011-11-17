<?php

class ComContentDatabaseDocumentTypes extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->name = 'content_types';

		parent::_initialize($config);
	}
}