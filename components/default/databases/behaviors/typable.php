<?php

class ComDefaultDatabaseBehaviorTypable extends KDatabaseBehaviorAbstract
{
    /**
     * Upon insert, forcefully inject the type of the content
     *
     * @return void
     */
    protected function _beforeDocumentInsert(KCommandContext $context)
    {
        // Get the page from the application router.
        $context->data->type = $context->caller->getType();
    }

    /**
     * Upon select, forcefully select the type of the content
     *
     * @return void
     */
    protected function _beforeDocumentFind(KCommandContext $context)
    {
        $context->query->where('type', '=', $context->caller->getType());
    }
}