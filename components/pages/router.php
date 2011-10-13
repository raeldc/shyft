<?php

class ComPagesRouter extends ComDefaultRouter
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				'new'         => 'view=page&layout=#form',
				'edit/<page>' => 'view=page&layout=#form&page=!',
				'<page>'      => 'view=#pages&layout=#default&page=!',
			),
		));

		parent::_initialize($config);
	}
}