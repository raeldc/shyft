<?php

class ComPagesRouter extends ComDefaultRouter
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				'new'       => 'view=page&layout=#form',
				'edit/<slug>' => 'view=page&layout=#form&slug=!',
			),
		));

		parent::_initialize($config);
	}
}