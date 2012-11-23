<?php

class SDatabaseFieldAuto extends SDatabaseFieldAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'filter' => 'string',
		));

		parent::_initialize($config);
	}

	protected function _process($value, KDatabaseRowAbstract $row)
	{
		return $value;
	}
}