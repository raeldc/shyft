<?php

class ComDefaultRouter extends SRouterDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				':view'          => 'view=:view',
				':view/new'      => 'view=:view&layout=form',
				':view/edit/:id' => 'view=:view&layout=form&id=:id',
			),
		));
	
		parent::_initialize($config);
	}
	
}