<?php

class ComPagesDatabaseRowPage extends SDatabaseRowDefault
{
	/**
     * Get a value by key, returning a fetched data if it's a reference
     *
     * @param   string  The key name
     */
	public function __get($key)
	{
		if ($key == 'type') 
		{
			$result = parent::__get($key);

			$type          = clone $this->getIdentifier();
			$type->package = 'content';
			$type->name    = 'types';
			$type          = $this->getService($type);

			if ($result) {
				$type->setData(array('id' => $result))->load();
			}

			return $type;
		}
		else return parent::__get($key);
	}
}