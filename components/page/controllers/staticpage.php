<?php

class ComStaticpageControllerStaticpage extends ComDefaultControllerDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->request->view = 'staticpage';
		
		parent::_initialize($config);
	}
	
}