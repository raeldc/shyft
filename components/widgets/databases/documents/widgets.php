<?php

class ComWidgetsDatabaseDocumentWidgets extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->name = 'widgets';
	
		parent::_initialize($config);
	}
	
}