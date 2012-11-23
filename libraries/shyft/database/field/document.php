<?php

class SDatabaseFieldDocument extends SDatabaseFieldCommandchain
{
	protected $_document;

	protected $_identifier_field;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		if (empty($config->document)) {
			throw new KDatabaseException('Document field must be configured to have a document');
		}

		$this->_document         = $config->document;
		$this->_identifier_field = $config->identifier_field;
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'identifier_field' => 'id',
			'filter'           => 'string',
            'document'         => KInflector::pluralize($config->name),
		));

		parent::_initialize($config);
	}


	protected function _process($value, KDatabaseRowAbstract $row)
	{
		if($value instanceof KDatabaseRowAbstract){
			return $value;
		}

		$table = $this->getService('com://site/mcced.database.document.'.$this->_document);

		if(is_string($value) || $value instanceof MongoId)
		{
			$query = $table->getQuery();

			$query->field($this->_identifier_field)
				->equalTo((string)$value);

			// Directly query the table
			$value = $table->find($query, KDatabase::FETCH_ROW);
		}
        elseif(is_array($value))
        {
            $value = $table->getRow()->setData($value);
        }
		else $value = $table->getRow();

		return $value;
	}

	protected function _beforeDocumentInsert(KCommandContext $context)
	{
		$this->disableProcessing();

        if ($context->data->{$this->name} instanceof KDatabaseRowAbstract) {
            $context->data->{$this->name} = (string)$context->data->{$this->name}->{$this->_identifier_field};
        }
	}

	protected function _beforeDocumentUpdate(KCommandContext $context)
	{
		$this->_beforeDocumentInsert($context);
	}

	protected function _afterDocumentInsert(KCommandContext $context)
	{
		$this->enableProcessing();
	}
}