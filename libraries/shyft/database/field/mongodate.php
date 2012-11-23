<?php

class SDatabaseFieldMongodate extends SDatabaseFieldCommandchain
{
	protected function _afterDocumentFind(KCommandContext $context)
	{
		if($context->mode === KDatabase::FETCH_ROW)
		{
			$date = $context->data->{$this->name};
			$this->disableProcessing();

			$context->data->setMappedData(array(
				$this->name => $date
			));

			$this->enableProcessing();
		}
	}

	protected function _beforeDocumentInsert(KCommandContext $context)
	{
		$this->disableProcessing();
		if ($context->data->{$this->name} != '')
			$context->data->{$this->name} = new MongoDate(strtotime($context->data->{$this->name}));
	}

	protected function _beforeDocumentUpdate(KCommandContext $context)
	{
		$this->disableProcessing();
		if($context->data->isModified($this->name))
			$context->data->{$this->name} = new MongoDate(strtotime($context->data->{$this->name}));
	}

	protected function _process($value, KDatabaseRowAbstract $row)
	{
		if ($value instanceof MongoDate)
		{
			list($usec, $time) = explode(' ',$value);
			return strftime('%F',$time);
		}

		return $value;
	}

	protected function _toArray($value, KDatabaseRowAbstract $row)
	{
		return $this->_process($value, $row);
	}
}
