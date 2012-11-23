<?php

class SDatabaseFieldDocuments extends SDatabaseFieldDocument
{
    protected $_document;

    protected $_identifier_field;

    protected function _process($value, KDatabaseRowAbstract $row)
    {
        if($value instanceof KDatabaseRowsetAbstract){
            return $value;
        }

        $table = $this->getService('com://site/mcced.database.document.'.$this->_document);

        if(is_array($value))
        {
            $query = $table->getQuery();

            $query->field($this->_identifier_field)
                ->in((array)$value);

            // Directly query the table
            $value = $table->find($query, KDatabase::FETCH_ROWSET);
        }
        else $value = $table->getRowset();

        return $value;
    }

    protected function _beforeDocumentInsert(KCommandContext $context)
    {
        $this->disableProcessing();

        if ($context->data->{$this->name} instanceof KDatabaseRowsetAbstract) {
            $value = array();
            foreach ($context->data->{$this->name} as $row) {
                $value[] = (string)$row->{$this->_identifier_field};
            }
            $context->data->{$this->name} = $value;
        }
    }
}