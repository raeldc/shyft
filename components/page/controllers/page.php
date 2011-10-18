<?php

class ComPageControllerPage extends ComDefaultControllerDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->request->view = 'page';
		
		parent::_initialize($config);
	}
	
}