<?php

class ComContentDatabaseSchemaContents extends KObject
{
	protected function _initialize(KConfig $config)
	{
		$config->fields = array(
			'id' => array(
				'unique' => true,
				'type' => 'identifier'
			),
			'title' => array(
				'type' => 'string',
				'filter' => 'string'
			),
			'body' => array(
				'type' => 'hasone',
				'model' => 'com://site/staticpage.model.staticpages'
			)
		);

		parent::_initialize($config);
	}
}