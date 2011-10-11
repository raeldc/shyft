<?php

/*
 * Model to get a single page. 
 * This is used by the application dispatcher to get the current page.
 */
class ComPagesModelPages extends SModelDefault
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_state->insert('enabled', 'cmd', true);
	}

	protected function _buildQueryWhere(SDatabaseQueryDocument $query)
	{
		if ($this->_state->enabled) {
			$query->where('enabled', '=', true);
		}
	}
}