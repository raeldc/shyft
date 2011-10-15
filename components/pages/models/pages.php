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
	
		$this->_state
			->insert('enabled', 'boolean', false)
			->insert('page', 'cmd', '', true);
	}

	protected function _buildQueryWhere(SDatabaseQueryDocument $query)
	{
		if (!empty($this->_state->page)) {
			$query->where('permalink', '=', $this->_state->page);
		}

		if ($this->_state->enabled) {
			$query->where('enabled', '=', true);
		}
	}
}