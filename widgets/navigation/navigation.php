<?php

class WidgetNavigation extends WidgetDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'model' => 'pages',
			'view'	=> 'pages'
		));

		parent::_initialize($config);
	}
	
}