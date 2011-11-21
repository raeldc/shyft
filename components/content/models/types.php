<?php

class ComContentModelTypes extends ComDefaultModelDefault
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_state->insert('type', 'slug');
	}
	
	public function _buildQueryWhere(SDatabaseQueryDocument $query)
	{
		parent::_buildQueryWhere($query);
		
		if ($this->_state->type) {
			$query->where('type', '=', $this->_state->type);
		}
	}
}