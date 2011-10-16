<?php

class ComContentRouter extends ComDefaultRouter
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				'new'        => 'view=content&layout=#form',
				'edit/<id>'  => 'view=content&layout=#form&id=!',
				'[<layout>]' => 'view=#contents&layout=!',
			),
			'defaults' => array(
				'view'   => 'contents',
				'layout' => 'default'
			)
		));

		parent::_initialize($config);
	}
}