<?php

class SDatabaseRowDocument extends KDatabaseRowAbstract
{
	/**
     * Document object or identifier (type://app/COMPONENT.database.document.DOCUMENTNAME)
     *
     * @var string|object
     */
    protected $_document = false;


	/**
     * Object constructor 
     *
     * @param   object  An optional KConfig object with configuration options.
     */
	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		$this->_document = $config->document;
			
		// Reset the row
        $this->reset();
            
        // Reset the row data
        if(isset($config->data))  {
            $this->setData($config->data->toArray(), $this->_new);
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
			'document'	=> $this->_identifier->name
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
		            $this->_document = KFactory::get($this->_document);
                } catch (KDatabaseDocumentException $e) {
                    $this->_document = false;
                }
            }
        }

        return $this->_document;
    }

    /**
     * Method to set a document object attached to the model
     *
     * @param   mixed   An object that implements KObjectIdentifiable, an object that
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
		        $identifier         = clone $this->_identifier;
		        $identifier->path   = array('database', 'document');
		        $identifier->name   = KInflector::tableize($document);
		    }
		    else  $identifier = KIdentifier::identify($document);

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
		        $row = $this->getDocument()->select($this->getData(true), KDatabase::FETCH_ROW);

		        // Set the data if the row was loaded succesfully.
		        if(!$row->isNew())
		        {
			        $this->setData($row->toArray(), false);
			        $this->_modified = array();
			        $this->_new      = false;
			    
			        $this->setStatus(KDatabase::STATUS_LOADED);
			        $result = $this;
		        }
            }
		}
	
		return $result;
	}

	/**
	 * Reset the row data using the defaults
	 *
	 * @return boolean	If successfull return TRUE, otherwise FALSE
	 */
	public function reset()
	{
		$result = parent::reset();
		
		if($this->isConnected())
		{
			// TODO: Get defaults from the row's field definition
			/*
	        if($this->_data = $this->getDefaults()) {
		        $result = true;
		    }
		    */

		    $result = true;
		}
		
		return $result;
	}

	/**
	 * Count the rows in the database based on the data in the row
	 *
	 * @return integer
	 */
	public function count()
	{
		$result = false;
	    
	    if($this->isConnected())
		{
	        //$data   = $this->filter($this->getData(true), true);
		    $result = $this->getDocument()->count($data);
		}

		return $result;
	}

	/**
     * Set the row data
     *
     * @param   mixed   Either and associative array, an object or a KDatabaseRow
     * @param   boolean If TRUE, update the modified information for each column being set. 
     *                  Default TRUE
     * @return  KDatabaseRowAbstract
     */
     public function setData( $data, $modified = true )
     {
        if($data instanceof KDatabaseRowInterface) {
            $data = $data->toArray();
        } else {
            $data = (array) $data;
        }

        if (isset($data['_id']))
        {
        	$data['id'] = $data['_id'];
        	unset($data['_id']);
        }

        if($modified) 
        {
            foreach($data as $column => $value) 
            {
                $this->$column = $value;
            }
        }
        else
        {
            $this->_data = array_merge($this->_data, $data);
        }
        
        return $this;
    }

	/**
     * Set row field value
     * 
     * If the value is the same as the current value and the row is loaded from the database
     * the value will not be reset. If the row is new the value will be (re)set and marked
     * as modified
     *
     * @param   string  The column name.
     * @param   mixed   The value for the property.
     * @return  void
     */
    public function __set($column, $value)
    {
    	if ($column == '_id') $column = 'id';

        if(!isset($this->_data[$column]) || ($this->_data[$column] != $value) || $this->isNew()) 
        {
            parent::__set($column, $value);
          
            $this->_modified[$column] = true;
            $this->_status            = null;
        } 
    }

    /**
     * Unset a row field
     * 
     * @param   string  The column name.
     * @return  void
     */
    public function __unset($column)
    {
         parent::__unset($column);
         
         unset($this->_modified[$column]);
    }
}