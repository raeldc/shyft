<?php

class ComPageModelPages extends ComDefaultModelDefault
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_state = new KConfigState();

		$this->_state->insert('page', 'cmd', null, true);
	}

	protected function _buildQueryWhere(SDatabaseQueryDocument $query)
	{
		if ($this->_state->page) {
			$query->where('page', '=', $this->_state->page);
		}
	}

	public function getItem()
	{
		// If there is no match for the page, reset the state so we get a new row
		if (!parent::getTotal()) {
			$this->_state->reset();
		}

		return parent::getItem();
	}
	
}