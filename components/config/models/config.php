<?php

class ComConfigModelConfig extends FluxModelDocument
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_state->insert('name', 'cmd', null, true);
	}
	
}