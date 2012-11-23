<?php

abstract class SDatabaseDocumentEmbedded extends SDatabaseDocumentAbstract
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if (empty($config->parent_document)) {
            throw new KDatabaseException("Embedded document must have a parent document");
        }

        $this->_parent_document = $config->parent_document;
    }

    protected function _initialize(KConfig $config)
    {
        $package = $this->getIdentifier()->package;
        $name    = $this->getIdentifier()->name;

        $config->append(array(
            'name' => $name,
            'parent_document' => ''
        ));

        parent::_initialize($config);
    }

    public function find($parent)
    {
        //Create commandchain context
        $context            = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_SELECT;

        if($this->getCommandChain()->run('before.find', $context) !== false)
        {
            if(isset($parent->{$this->getName()}))
            {
                $data = $parent->{$this->getName()};

                // Finding embedded docs will always return a rowset. It's up to the model to filter out data.
                $context->data = $this->getRowset();
                $context->data->addMappedData((array)$data);
            }

            $this->getCommandChain()->run('after.find', $context);

            $this->getParentDocument()->getCommandChain()->enable();
        }

        return $context->data;
    }

    /**
     * Table insert method
     *
     * @param  object       A KDatabaseRow object
     * @return bool|integer Returns the number of rows inserted, or FALSE if insert query was not executed.
     */
    public function insert(KDatabaseRowInterface $row)
    {
        $document = $this->getParentDocument();
        $parent = $row->{KInflector::singularize($document->getName())};

        if (!($parent instanceof KDatabaseRowInterface)) {
            throw new KDatabaseException("Embedded document must have a parent document in the form");
        }

        //Create commandchain context
        $context                  = $this->getCommandContext();
        $context->operation       = KDatabase::OPERATION_INSERT;
        $context->data            = $row;
        $context->query           = $this->getQuery();
        $context->name            = $this->getName();
        $context->parent_document = $parent;

        if($this->getCommandChain()->run('before.insert', $context) !== false)
        {
            $parent->getDocument()->getCommandChain()->disable();

            // Append the data into the field
            $context->query->update($this->getName())->append($context->data->getMappedData());

            foreach ($parent->getSchema()->getUniqueFields() as $field)
            {
                $key = $field->name;
                $context->query->field($key)->equalTo($parent->$key);
            }

            //Execute the insert query
            $document->getDatabase()->update($context->query);

            $context->data->setStatus(KDatabase::STATUS_CREATED);

            $this->getCommandChain()->run('after.insert', $context);

            $parent->getDocument()->getCommandChain()->enable();
        }

        return $context->data;
    }

    /**
     * Table update method
     *
     * @param  object           A KDatabaseRow object
     * @return boolean|integer  Returns the number of rows updated, or FALSE if insert query was not executed.
     */
    public function update(KDatabaseRowInterface $row)
    {
        //Create commandchain context
        $context            = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_UPDATE;
        $context->data      = $row;
        $context->name      = $this->getName();
        $context->query     = $this->getParentDocument()->getQuery();

        if($this->getCommandChain()->run('before.update', $context) !== false)
        {
            if (!$row->isNew())
            {
                $this->getParentDocument()->getCommandChain()->disable();

                $data = $context->data->getModifiedData();

                foreach($data as $field => $value){
                    $context->query->update($this->getName().'.$.'.$field)->change($value);
                }

                foreach ($this->getSchema()->getUniqueFields() as $field)
                {
                    $context->query->field($this->getName().'.'.$field->field)->equalTo($context->data->{$field->name});
                }

                $parent = $context->data->{KInflector::singularize($this->getParentDocument()->getName())};

                foreach ($parent->getDocument()->getSchema()->getUniqueFields() as $field)
                {
                    $key = $field->name;
                    $context->query->field($key)->equalTo($parent->$key);
                }

                //Execute the update query
                $context->affected = $this->getParentDocument()->getDatabase()->update($context->query);

                if(((integer)$context->affected) > 0)
                {
                    //Reverse apply the column mappings and set the data in the row
                    $context->data->setStatus(KDatabase::STATUS_UPDATED);
                }
                else $context->data->setStatus(KDatabase::STATUS_FAILED);
            }
            else $context->data->setStatus(KDatabase::STATUS_FAILED);

            $this->getCommandChain()->run('after.update', $context);
            $this->getParentDocument()->getCommandChain()->enable();
        }

        return $context->affected;
    }

    /**
     * Table delete method
     *
     * @param  object       A KDatabaseRow object
     * @return bool|integer Returns the number of rows deleted, or FALSE if delete query was not executed.
     */
    public function delete(KDatabaseRowInterface $row)
    {
        //Create commandchain context
        $context            = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_DELETE;
        $context->data      = $row;
        $context->name      = $this->getName();
        $context->affected  = false;
        $context->query     = $this->getParentDocument()->getQuery();

        if($this->getCommandChain()->run('before.delete', $context) !== false)
        {
            if (!$row->isNew())
            {
                $this->getParentDocument()->getCommandChain()->disable();

                $parent = $context->data->{KInflector::singularize($this->getParentDocument()->getName())};

                // Generate the query "update"
                foreach ($this->getSchema()->getUniqueFields() as $field)
                {
                    $key = $field->name;
                    $context->query->update($this->getName())->pull(array($field->field => $context->data->$key));
                    // Just one condition should suffice
                    break;
                }

                // Generate query "where" - target is the parent document.
                foreach ($parent->getDocument()->getSchema()->getUniqueFields() as $field)
                {
                    $key = $field->name;
                    $context->query->field($key)->equalTo($parent->$key);
                }

                //Execute the update query
                $context->affected = $this->getParentDocument()->getDatabase()->update($context->query);

                if(((integer) $context->affected) > 0)
                {
                    //Reverse apply the column mappings and set the data in the row
                    $context->data->setStatus(KDatabase::STATUS_DELETED);
                }
                else $context->data->setStatus(KDatabase::STATUS_FAILED);
            }
            else $context->data->setStatus(KDatabase::STATUS_FAILED);

            $this->getCommandChain()->run('after.delete', $context);
            $this->getParentDocument()->getCommandChain()->enable();
        }

        return $context->affected;
    }

    /**
     * Count Results of the Query
     *
     * @param   mixed   KDatabaseQuery object or query string or null to count all rows
     * @return  int     Number of rows
     */
    public function count($query = null)
    {
        return $this->getDatabase()->count($this->getQuery($query));
    }

    public function getParentDocument()
    {
        if(!($this->_parent_document instanceof SDatabaseDocumentAbstract))
        {
            $identifier = clone $this->getIdentifier();
            $identifier->name = $this->_parent_document;

            $this->_parent_document = $this->getService($identifier);
        }

        return $this->_parent_document;
    }

    public function getQuery($query = null)
    {
        return $this->getParentDocument()->getQuery();
    }
}