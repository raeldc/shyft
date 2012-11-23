<?php

class ComDefaultDatabaseSchemaDefault extends SDatabaseSchemaAbstract
{

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'dynamic' => true,
			'fields' => array(
				'id' => array(
					'type' => 'mongoid'
				),
                'title' => array(
                    'type' => 'string',
                ),
                'slug' => array(
                    'type' => 'slug',
                ),
                '_token' => array(
                    'type' => 'string',
                    'indb' => false,
                ),
                'action' => array(
                    'type' => 'cmd',
                    'indb' => false,
                )
		)));

		parent::_initialize($config);
	}
}