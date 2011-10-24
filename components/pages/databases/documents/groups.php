<?php

class ComPagesDatabaseDocumentGroups extends SDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->identity_column = 'slug';
		$config->name = 'pages_groups';

		$sluggable = $this->getBehavior('sluggable', array(
			'columns' => array('name')
		));

		$config->behaviors = array($sluggable);

		parent::_initialize($config);
	}
}