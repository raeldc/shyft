<?php

class SDatabaseFieldInt extends SDatabaseFieldAbstract
{

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'filter'	=> 'int'
		));

		parent::_initialize($config);
	}

	protected function _process($value, KDatabaseRowAbstract $row)
	{
		return $this->filterValue($value);
	}
}