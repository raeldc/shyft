<?php

class ComDefaultDatabaseBehaviorPageable extends KDatabaseBehaviorAbstract
{   
    /**
     * Make sure that the data will have the page information
     *
     * @return void
     */
    protected function _beforeDocumentInsert(KCommandContext $context)
    {
        // Make sure that page information
        $context->data->page = $this->getService('com://site/application.router')->application->page;
    }

    /**
     * Upon select, forcefully select the page
     *
     * @return void
     */
    protected function _beforeDocumentFind(KCommandContext $context)
    {
        $context->query->where('page', '=', $this->getService('com://site/application.router')->application->page);
    }
}