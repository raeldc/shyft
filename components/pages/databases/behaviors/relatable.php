<?php

class ComPagesDatabaseBehaviorRelatable extends KDatabaseBehaviorAbstract
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_relationships = $config->relationships;
	}
	
	protected function _initialize(KConfig $config)
    {
    	$config->append(array(
			'priority'   => KCommand::PRIORITY_LOW,
			// array field => collection
			'relationships' => array('content' => KFactory::get('com://site/content.model.contents')),
	  	));

    	parent::_initialize($config);
   	}

   	protected function _afterDocumentFind(KCommandContext $context)
	{
		if ($context->data instanceof SDatabaseRowDocument) 
		{

		}

		return true;
	}
	
	protected function _beforeDocumentInsert(KCommandContext $context)
	{
		foreach ($relationships as $field => $value) 
		{
			$context->data->$field = $context->caller->getDatabase()->createRelationship();
		}

		return true;
	}
}