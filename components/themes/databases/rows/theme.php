<?php

class ComThemesDatabaseRowTheme extends SDatabaseRowDocument
{
	/**
     * Get the related value layout
     *
     * @param   string  The key name
     */
	public function __get($key)
	{
		if ($key == 'layouts') 
		{
			$result = parent::__get($key);

			$layouts       = clone $this->getIdentifier();
			$layouts->path = array('database', 'rowset');
			$layouts->name = 'layouts';

			$layouts = $this->getService($layouts)
				->addData($this->manifest['layouts']);

			return $layouts;
		}
		else return parent::__get($key);
	}
}