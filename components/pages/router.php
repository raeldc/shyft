<?php

class ComPagesRouter extends ComDefaultRouter
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				'new'       => 'view=page&layout=#form',
				'edit/<id>' => 'view=page&layout=#form&id=!',
				'<layout>'  => 'view=#pages&layout=default',
			),
		));

		parent::_initialize($config);
	}
}