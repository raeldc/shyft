<?php

class SDatabaseFieldCreated extends SDatabaseFieldMongotimestamp
{
	protected function _beforeDocumentInsert(KCommandContext $context)
	{
        $this->disableProcessing();
		$context->data->{$this->name} = new MongoDate();
	}
}
