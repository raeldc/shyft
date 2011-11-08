<?php

class ComPagesDatabaseDocumentGroups extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->name = 'pages_groups';

		parent::_initialize($config);
	}
}