<?php

class ComStaticpageRouter extends ComDefaultRouter
{
	protected function _initialize(KConfig $config)
	{
		$config->routes = array(
			'<id>'                          => 'view=staticpage&id=:id',
			'pages[/page-<page>][/<limit>]' => 'view=staticpages&page=1&limit=10',
		);

		parent::_initialize($config);
	}
}