<?php

class ComPagesRouter extends ComDefaultRouter
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			// The router will pass the route only if the first segment is its alias
			'alias' => 'pages',

			// string after : is a parameter
			// string after # is the filter that will be used for the parameter. # is legal after :paramter e.g. :paramter#filter
			// string inside [] is optional
			'routes' => array(
				'manage/<id>[/<uri>]' => 'mode=manage&view=page&id=:id&layout=default',
			),

			// Regex that will be applied to check a parameter
			'regex' => array(
				'uri' => '.*'
			)
		));

		parent::_initialize($config);
	}
}