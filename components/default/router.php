<?php

class ComDefaultRouter extends SRouterDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				'[<view>[/<layout>]]' => 'view=!&layout=default',
			),
		));

		parent::_initialize($config);
	}
	
}