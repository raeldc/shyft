<?php

class ComWorkflowDatabaseDocumentWorkflows extends FlowDatabaseDocumentDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'name' => 'workflows',
		));
	
		parent::_initialize($config);
	}
	
}