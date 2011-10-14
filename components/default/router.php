<?php

class ComDefaultRouter extends SRouterDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				'[<view>[/<layout>]]' => 'view=!&layout',
			),
		));
	
		parent::_initialize($config);
	}
	
}