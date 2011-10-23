<?php

class ComPagesDatabaseDocumentGroups extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->identity_column = 'slug';
		$config->name = 'pages_groups';

		//$config->behaviors = array('sluggable');

		parent::_initialize($config);
	}
}