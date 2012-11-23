<?php

class SDatabaseFieldArray extends SDatabaseFieldAbstract
{

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'filter'	=> 'string'
		));

		parent::_initialize($config);
	}

	protected function _process($value, KDatabaseRowAbstract $row)
	{
		$array = array();

		if(is_array($value))
		{
			foreach($value as $content)
			{
				if(!empty($content)){
					$array[] = $this->filterValue($content);
				}
			}
		}

		// Filter array. Removed empty and null values in array.
		$array = array_filter($array);

		return $array;
	}
}