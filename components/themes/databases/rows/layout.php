<?php

class ComThemesDatabaseRowLayout extends SDatabaseRowDocument
{
	/**
     * Get the related value containers
     *
     * @param   string  The key name
     */
	public function __get($key)
	{
		if ($key == 'containers') 
		{
			$result = parent::__get($key);

			$containers       = clone $this->getIdentifier();
			$containers->path = array('database', 'rowset');
			$containers->name = 'containers';

			$containers = $this->getService($containers)
				->addData(parent::__get($key));

			return $containers;
		}
		else return parent::__get($key);
	}
}