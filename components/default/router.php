<?php

class ComDefaultRouter extends SRouterDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'alias'  => $this->getIdentifier()->package,
			'routes' => array(),
		));
	
		parent::_initialize($config);
	}
	
}