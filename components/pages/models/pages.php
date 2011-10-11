<?php

/*
 * Model to get a single page. 
 * This is used by the application dispatcher to get the current page.
 */
class ComPagesModelPage extends SModelDefault
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_state->insert('page', 'cmd', null, true);
	}

	public function getList()
	{
		// In getting a list, we don't want to specify a page so we set it to null
		$this->_state->page = null;

		return parent::getList();
	}

	protected function _buildQueryWhere(SDatabaseQueryDocument $query)
	{
		if ($this->_state->page === 'default') {
			$query->where('default', '=', true);
		}
		elseif (!empty($this->_state->page)) {
			$query->where('permalink', '=', $this->_state->page);
		}
	}
}