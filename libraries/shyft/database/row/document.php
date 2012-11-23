<?php

abstract class SDatabaseRowDocument extends KDatabaseRowAbstract
{
	/**
     * Document object or identifier (type://app/COMPONENT.database.document.DOCUMENTNAME)
     *
     * @var string|object
     */
    protected $_document = false;

    /**
     * Tracks columns where data has been updated. Allows more specific
     * save operations.
     *
     * @var array
     */
    protected $_modified = array();

    /**
     * Tracks the status the row
     *
     * Available row status values are defined as STATUS_ constants in KDatabase
     *
     * @var string
     * @see KDatabase
     */
    protected $_status = null;

    /**
     * The status message
     *
     * @var string
     */
    protected $_status_message = '';

    /**
     * Tracks if row data is new
     *
     * @var bool
     */
    protected $_new = true;

	/**
     * Object constructor
     *
     * @param   object  An optional KConfig object with configuration options.
     */
	public function __construct(KConfig $config = null)
	{
		//If no config is passed create it
        if(!isset($config)) $config = new KConfig();

		KObjectArray::__construct($config);

        $this->_document = $config->document;

		// Reset the row
        $this->reset();

        // Set the new state of the row
        $this->_new = (bool)$config->new;

        //Set the status
        if(isset($config->status)) {
            $this->setStatus($config->status);
        }

        // Reset the row data
        if(isset($config->data))
        {
            $this->setData(KConfig::unbox($config->data), $this->_new);
        }

        //Set the status message
        if(!empty($config->status_message)) {
            $this->setStatusMessage($config->status_message);
        }
	}

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'data'            => null,
            'new'             => true,
			'document'        => $this->getIdentifier()->name,
			'status'          => null,
            'status_message'  => '',
		));

		parent::_initialize($config);
	}

	/**
	 * Saves the row to the database.
	 *
	 * This performs an intelligent insert/update and reloads the properties
	 * with fresh data from the table on success.
	 *
	 * @return boolean	If successfull return TRUE, otherwise FALSE
	 */
	public function save()
	{
	    $result = false;

	    if($this->isConnected())
	    {
	        if($this->_new) {
	            $result = $this->getDocument()->insert($this);
		    } else {
		        $result = $this->getDocument()->update($this);
		    }
	    }

		return (bool) $result;
    }

    /**
	 * Deletes the row from the database.
	 *
	 * @return boolean	If successfull return TRUE, otherwise FALSE
	 */
	public function delete()
	{
		$result = false;

		if($this->isConnected())
		{
            if(!$this->_new)
		    {
		        $result = $this->getDocument()->delete($this);

		        if($result !== false)
	            {
	                if(((integer) $result) > 0) {
	                    $this->_new = true;
	                }
                }
		    }
		}

		return (bool) $result;
	}

    /**
	 * Test the connected status of the row.
	 *
	 * @return	boolean	Returns TRUE if we have a reference to a live KDatabaseDocumentAbstract object.
	 */
    public function isConnected()
	{
	    return (bool) $this->getDocument();
	}

    /**
     * Method to get a document object
     *
     * Function catches KDatabaseDocumentExceptions that are thrown for documents that
     * don't exist. If no document object can be created the function will return FALSE.
     *
     * @return KDatabaseDocumentAbstract
     */
    public function getDocument()
    {
        if($this->_document !== false)
        {
            if(!($this->_document instanceof SDatabaseDocumentAbstract))
		    {
		        //Make sure we have a document identifier
		        if(!($this->_document instanceof KIdentifier)) {
		            $this->setDocument($this->_document);
			    }

		        try {
		            $this->_document = $this->getService($this->_document);
                } catch (KDatabaseDocumentException $e) {
                    $this->_document = false;
                }
            }
        }

        return $this->_document;
    }

    public function getTable()
    {
    	return $this->getDocument();
    }

    /**
     * Method to set a document object attached to the model
     *
     * @param   mixed   An object that implements KObject, an object that
     *                  implements KIdentifierInterface or valid identifier string
     * @throws  KDatabaseRowsetException    If the identifier is not a document identifier
     * @return  KModelDocument
     */
    public function setDocument($document)
	{
		if(!($document instanceof SDatabaseDocumentAbstract))
		{
			if(is_string($document) && strpos($document, '.') === false )
		    {
		        $identifier         = clone $this->getIdentifier();
		        $identifier->path   = array('database', 'document');
		        $identifier->name   = KInflector::tableize($document);
		    }
		    else  $identifier = $this->getIdentifier($document);

			if($identifier->path[1] != 'document') {
				throw new KDatabaseRowsetException('Identifier: '.$identifier.' is not a document identifier');
			}

			$document = $identifier;
		}

		$this->_document = $document;

		return $this;
	}

	/**
	 * Load the row from the database using the data in the row
	 *
	 * @return object	If successfull returns the row object, otherwise NULL
	 */
	public function load()
	{
		$result = null;

		if($this->_new)
		{
            if($this->isConnected())
            {
                $row = $this->getDocument()->find($this->getData(), KDatabase::FETCH_ROW);

		        if(!$row->isNew())
		        {
			        $this->setData($row, false);
			        $this->_modified = array();
			        $this->_new      = false;

			        $this->setStatus(KDatabase::STATUS_LOADED);
			        $result = $this;
		        }
            }
		}

		return $result;
	}

	public function getModified()
    {
        return array_keys($this->_modified);
    }

    /**
    * Returns an associative array of the unmapped data
    *
    * @param   boolean  If TRUE, only return the modified data. Default FALSE
    * @return  array
    */
    public function getData($modified = false)
    {
        if($modified) {
            $result = array_intersect_key($this->_data, $this->_modified);
        } else {
            $result = $this->_data;
        }

        $schema = $this->getSchema();
        $data = array();

        foreach($result as $key => $value)
        {
            $field = $schema->getField($schema->getFieldName($key));

            if($field && $field->isIndb()) {
                $data[$field->field] = $field->processValue($value, $this);
            }
        }

        return $data;
    }

    public function getRawData()
    {
        return $this->_data;
    }

    public function getMappedData()
    {
        return $this->getData();
    }

    /**
     * Return all modified data mapped into real field names
     * @return array An associative array where keys are the real field name in DB
     */
    public function getModifiedData()
    {
    	return $this->getData(true);
    }

    /**
     * Set the row data. Data set here will be marked as modified always.
     *  The keys of the data are expected to be the unmapped field names
     *
     * @param   mixed   Either and associative array, an object or a KDatabaseRow
     * @return  KDatabaseRowAbstract
     */
    public function setData($data, $modified = true)
    {
        if($data instanceof KObjectArray) {
            $data = $data->toArray();
        } else {
            $data = (array) $data;
        }

        if($modified)
        {
            foreach($data as $column => $value)
            {
                $this->$column = $value;
            }
        }
        else $this->setMappedData($data);

        return $this;
    }

    /**
     * Set raw data from database.
     * @param $data     Data from database
     */
    public function setMappedData($data)
    {
        if($data instanceof KObjectArray) {
            $data = $data->toArray();
        } else {
            $data = (array) $data;
        }

        $schema = $this->getSchema();

        foreach($data as $key => $value)
        {
            $field = $schema->getFieldName($key);
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * Set the status
     *
     * @param   string|null     The status value or NULL to reset the status
     * @return  KDatabaseRowAbstract
     */
    public function setStatus($status)
    {
        $this->_status   = $status;

        // @TODO: Find a better way for validation. This method shoulnd't be here.
        if($status !== 'validate_failed') {
            $this->_new      = false;
        }

        if($status != KDatabase::STATUS_FAILED) {
            $this->_modified = array();
        }

        if($status == KDatabase::STATUS_DELETED) {
            $this->_new = true;
        }

        return $this;
    }

    public function getSchema()
    {
        return $this->getDocument()->getSchema();
    }

	/**
     * Returns the status
     *
     * @return string The status
     */
    public function getStatus()
    {
        return $this->_status;
    }

    public function getIdentityColumn()
    {
        return $this->getSchema()->getPrimaryKey();
    }

    public function toArray()
    {
        $schema = $this->getSchema();
        $data = array();

        foreach($this->_data as $key => $value)
        {
            $name = $schema->getFieldName($key);
            $field = $schema->getField($name);

            if($field){
                $value = $field->processToArray($value, $this);
            }

            $data[$name] = $value;
        }

        return $data;
    }

	public function __set($key, $value)
    {
        $schema = $this->getSchema();

    	if ($field = $schema->getField($key))
    	{
            // Always set the field mapped to its db field
            if(!isset($this->_data[$field->field]) || ($this->_data[$field->field] !== $value) || $this->isNew())
            {
                parent::__set($field->field, $value);

                $this->_modified[$field->field] = true;
            }
    	}
    }

    /**
     * Test existence of a key
     *
     * @param  string  The key name.
     * @return boolean
     */
    public function __isset($key)
    {
        return ($this->getSchema()->hasField($key)) ? true : parent::__isset($key);
    }

    public function __get($field)
    {
    	if ($field = $this->getSchema()->getField($field))
        {
            // Get mapped field
            $value  = parent::__get($field->field);

            // Get the processed value
            $result = $field->processValue($value, $this);

            // Cache the processed value
            $this->_data[$field->field] = $result;

            return $result;
    	}

    	return null;
    }
}