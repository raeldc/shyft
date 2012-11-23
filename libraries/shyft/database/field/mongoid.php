<?php

class SDatabaseFieldMongoid extends SDatabaseFieldCommandchain
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'primary' => true,
			'field'   => '_id',
			'filter'  => 'string'
		));

		parent::_initialize($config);
	}

	protected function _toArray($value, KDatabaseRowAbstract $row)
	{
		return (string)$value;
	}

	protected function _forQuery($value){
		return new MongoId((string)$value);
	}

	protected function _beforeDocumentInsert(KCommandContext $context)
	{
		$context->data->{$this->name} = new MongoId();
	}
}