<?php

class ComPagesDatabaseDocumentGroups extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->name = 'page_groups';

		parent::_initialize($config);
	}
}