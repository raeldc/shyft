<?php

class ComDashboardControllerDashboard extends ComDefaultControllerDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->request->view = 'dashboard';
	
		parent::_initialize($config);
	}
	
}