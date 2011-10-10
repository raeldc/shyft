<?php

class ComStaticpageRouter extends ComDefaultRouter
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			// The router will pass the route only if the first segment is its alias
			'alias' => 'page',

			// string after : is a parameter
			// string after # is the filter that will be used for the parameter. # is legal after :paramter e.g. :paramter#filter
			// string inside [] is optional
			'routes' => array(
				'<id>'                    => 'view=staticpage&id=:id',
				'pages[/page-<page>][/<limit>]' => 'view=staticpages&page=:page|1&limit=:limit|10',
			),
		));

		parent::_initialize($config);
	}	
}