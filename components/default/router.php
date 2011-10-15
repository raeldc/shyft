<?php

class ComDefaultRouter extends SRouterDefault
{
	protected function _initialize(KConfig $config)
	{
		if (!isset($config->routes)) 
		{
			$config->routes = array(
				'[<view>[/<layout>]]' => 'view=!&layout=default',
			);
		}

		parent::_initialize($config);
	}	
}