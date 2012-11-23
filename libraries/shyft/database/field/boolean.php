<?php

class SDatabaseFieldBoolean extends SDatabaseFieldAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'filter'	=> 'boolean'
		));

		parent::_initialize($config);
	}

	protected function _process($value, KDatabaseRowAbstract $row)
	{
		return $this->filterValue($value);
	}
}