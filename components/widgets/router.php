<?php

class ComWidgetsRouter extends ComDefaultRouter
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				'new'        => 'view=widget&layout=#form',
				'edit/<id>'  => 'view=widget&layout=#form&id=!',
				'[<layout>]' => 'view=#widgets&layout=!',
			),
		));

		parent::_initialize($config);
	}
}