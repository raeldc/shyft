<?php

class ComPageRouter extends ComDefaultRouter
{
	protected function _initialize(KConfig $config)
	{
		$config->routes = array(
			'[<layout>]' => 'view=#page&layout=!default',
		);

		parent::_initialize($config);
	}
}