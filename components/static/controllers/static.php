<?php

class ComStaticControllerStatic extends ComDefaultControllerDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->request->view = 'static';
		
		parent::_initialize($config);
	}
	
}