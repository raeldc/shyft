<?php

class ComDefaultModelNode extends SModelDocument
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_state->insert('page', 'cmd', '');
	}

	protected function _buildQueryWhere(SDatabaseQueryDocument $query)
	{
		parent::_buildQueryWhere($query);

		if (!empty($this->_state->page)) {
			$query->where('page', '=', $this->_state->page);
		}
	}
}