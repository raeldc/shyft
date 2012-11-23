<?php

abstract class SDatabaseDocumentAbstract extends KObject
{
    protected $_database;
    protected $_name;
    protected $_behaviors = array();
    protected $_schema;

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_database = $config->database;
        $this->_name = $config->name;

        // Mixin a command chain
        $this->mixin(new KMixinCommand($config->append(array('mixer' => $this))));

        // Mixin the behavior interface
        $this->mixin(new KMixinBehavior($config));
    }

    protected function _initialize(KConfig $config)
    {
        $database = $this->getService('com://site/default.database.adapter.mongo');
        $package = $this->getIdentifier()->package;
        $name    = $this->getIdentifier()->name;

        $config->append(array(
            'command_chain'     => $this->getService('koowa:command.chain'),
            'event_dispatcher'  => null,
            'dispatch_events'   => false,
            'enable_callbacks'  => false,
            'database'          => $database,
            'behaviors'         => array(),
            'name'              => $name,
        ));

        parent::_initialize($config);
    }

    public function find($query = null, $mode = KDatabase::FETCH_ROWSET)
    {
        //Create commandchain context
        $context            = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_SELECT;
        $context->query     = $this->getQuery($query);
        $context->mode      = $mode;

        if($this->getCommandChain()->run('before.find', $context) !== false)
        {
            switch($context->mode)
            {
                case KDatabase::FETCH_ROW:
                {
                    $context->data = $this->getRow();
                    // Execute the query
                    $data = $this->getDatabase()->find($context->query, $context->mode);

                    if(isset($data) && !empty($data)){
                       $context->data->setMappedData($data)->setStatus(KDatabase::STATUS_LOADED);
                    }

                    break;
                }

                case KDatabase::FETCH_ROWSET:
                {
                    $context->data = $this->getRowset();

                    // Execute the query
                    $data = $this->getDatabase()->find($context->query, $context->mode);

                    if(isset($data) && !empty($data)) {
                        $context->data->addMappedData($data);
                    }
                    break;
                }

                default : $context->data = $data;
            }

            $this->getCommandChain()->run('after.find', $context);
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
        //Create commandchain context
        $context            = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_INSERT;
        $context->data      = $row;
        $context->query     = null;
        $context->name      = $this->getName();

        if($this->getCommandChain()->run('before.insert', $context) !== false)
        {
            //Execute the insert query
            $data = $this->getDatabase()->insert($context->name, $context->data->getMappedData());

            $context->data->setMappedData($data)->setStatus(KDatabase::STATUS_CREATED);

            $this->getCommandChain()->run('after.insert', $context);
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
        $context->affected  = false;
        $context->query     = $this->getQuery();

        if($this->getCommandChain()->run('before.update', $context) !== false)
        {

            if (!$row->isNew())
            {
                foreach ($this->getSchema()->getUniqueFields() as $field)
                {
                    $key = $field->name;
                    $context->query->field($key)->equalTo($context->data->$key);
                }

                //Execute the update query
                $context->affected = $this->getDatabase()->update($context->query, $context->data->getModifiedData());

                if(((integer)$context->affected) > 0)
                {
                    //Reverse apply the column mappings and set the data in the row
                    $context->data->setStatus(KDatabase::STATUS_UPDATED);
                }
                else $context->data->setStatus(KDatabase::STATUS_FAILED);

                //Set the query in the context
                $context->query = $context->query;
            }
            else $context->data->setStatus(KDatabase::STATUS_FAILED);

            $this->getCommandChain()->run('after.update', $context);
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
        $context->query     = $this->getQuery();

        if($this->getCommandChain()->run('before.delete', $context) !== false)
        {
            if (!$row->isNew())
            {
                foreach ($this->getSchema()->getUniqueFields() as $field)
                {
                    $key = $field->name;
                    $context->query->field($key)->equalTo($context->data->$key);
                }

                //Execute the update query
                $context->affected = $this->getDatabase()->delete($context->query);

                if(((integer) $context->affected) > 0)
                {
                    //Reverse apply the column mappings and set the data in the row
                    $context->data->setStatus(KDatabase::STATUS_DELETED);
                }
                else $context->data->setStatus(KDatabase::STATUS_FAILED);
            }
            else $context->data->setStatus(KDatabase::STATUS_FAILED);

            $this->getCommandChain()->run('after.delete', $context);
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

    /**
     * Gets the table schema name without the table prefix
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get an instance of a row object for this table
     *
     * @param   array An optional associative array of configuration settings.
     * @return  KDatabaseRowInterface
     */
    public function getRow(array $options = array())
    {
        $identifier         = clone $this->getIdentifier();
        $identifier->path   = array('database', 'row');
        $identifier->name   = KInflector::singularize($identifier->name);

        //The row default options
        $options['document'] = $this;

        return $this->getService($identifier, $options);
    }

    /**
     * Get an instance of a rowset object for this table
     *
     * @param   array An optional associative array of configuration settings.
     * @return  KDatabaseRowInterface
     */
    public function getRowset(array $options = array())
    {
        $identifier         = clone $this->getIdentifier();
        $identifier->path   = array('database', 'rowset');

        //The rowset default options
        $options['document'] = $this;

        return $this->getService($identifier, $options);
    }

    public function getDatabase()
    {
        return $this->_database;
    }

    public function getSchema()
    {
        if(!$this->_schema)
        {
            $identifier         = clone $this->getIdentifier();
            $identifier->path   = array('database', 'schema');
            $identifier->name   = KInflector::singularize($identifier->name);

            //The row default options
            $options = array('document' => $this);

            $this->_schema = $this->getService($identifier, $options);
        }

        return $this->_schema;
    }

    public function getQuery($query = null)
    {
        if($query instanceof SDatabaseQueryAbstract)
        {
            $query->from($this->getName());
            return $query;
        }

        $builder = 'com://site/default.database.query.'.$this->getDatabase()->getIdentifier()->name;

        $option = array(
            'schema' => $this->getSchema()
        );

        if(is_numeric($query) || is_string($query) || (is_array($query) && is_numeric(key($query))))
        {
        	// @TODO: Create an schema::getUniqueField()
        	/*
            $query = $this->getService($builder, $option)
                          ->where
                          ->field($id)->equalTo($query);
			*/
        }
        elseif(is_array($query) && !is_numeric(key($query)))
        {
        	$values = $query;
            $query = $this->getService($builder, $option);

            foreach($values as $field => $value){
                $query->field($field)->equalTo($value);
            }
        }
        else $query = $this->getService($builder, $option);

        return $query->from($this->getName());
    }

    /**
     * Search the behaviors to see if this table behaves as.
     *
     * Function is also capable of checking is a behavior has been mixed succesfully
     * using is[Behavior] function. If the behavior exists the function will return
     * TRUE, otherwise FALSE.
     *
     * @param  string   The function name
     * @param  array    The function arguments
     * @throws BadMethodCallException   If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, array $arguments)
    {
        // If the method is of the form is[Bahavior] handle it.
        $parts = KInflector::explode($method);

        if($parts[0] == 'is' && isset($parts[1]))
        {
            if($this->hasBehavior(strtolower($parts[1]))) {
                 return true;
            }

            return false;
        }

        return parent::__call($method, $arguments);
    }
}