<?php

class ComPageDatabaseDocumentPage extends ComDefaultDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->identity_column = 'page';
	
		parent::_initialize($config);
	}
}