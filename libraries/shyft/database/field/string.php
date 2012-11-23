<?php

class SDatabaseFieldString extends SDatabaseFieldAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'filter' => 'string',
		));

		parent::_initialize($config);
	}
}