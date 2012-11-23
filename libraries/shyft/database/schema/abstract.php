<?php

abstract class SDatabaseSchemaAbstract extends KObject implements SDatabaseSchemaInterface
{
    /**
     * Array of Field objects or config
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Array of Fields mapped to its real field name in database
     *
     * @var array
     */
    protected $_field_map = array();

    /**
     * Array of unique fields
     *
     * @var array
     */
    protected $_unique_fields = array();

    /**
     * If schema is dynamic, and a field was undeclared, this will be used
     *
     * @var array
     */
    protected $_default_type = array();

    /**
     * Allow dynamic addition of new rows
     *
     * @var bool
     */
    protected $_dynamic = false;

    /**
     * The document where this schema is attached
     *
     * @var SDatabaseDocumentAbstract
     */
    protected $_document = false;

    protected $_primary_key;

    public function __construct(KConfig $config)
    {
    	parent::__construct($config);

        $this->_dynamic = (bool)$config->dynamic;

        $this->_default_type = $config->default_type;

		if($config->document instanceof SDatabaseDocumentAbstract){
			$this->_document = $config->document;
		}

        // Define the fields
		foreach ($config->fields as $key => $definition){
			$this->addField($key, $definition);
		}
    }

    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'document'     => '',
    		'fields'       => array(),
    		// If true, you are allowed to set fields on the fly using the default_type type
			'default_type' => array(
				'type'        => 'auto',
			),
    	))->append(array(
    		'dynamic' => (count($config->fields) === 0)
    	));

    	parent::_initialize($config);
    }

    public function addField($name, $definition = null)
	{
		if($this->hasField($name)){
			return $this;
		}

		if (is_null($definition) && !$this->_dynamic) {
			return false;
		}

		$definition = new KConfig($definition);

		$definition->name = $name;

		if (!$definition->type){
			$definition->append($this->_default_type);
		}

		// Created a field object based on the definition
		$identifier       = clone $this->getIdentifier();
	    $identifier->path = array('field');
	    $identifier->name = $definition->type;

		$field = $this->getService($identifier, $definition->toArray());

		$this->_fields[$field->name]    = $field;
		$this->_field_map[$field->field] = $field->name;

		// Insert the field into the document command chain if it is a command
		if($field instanceof KCommandInterface && !empty($this->_document)){
			$this->_document->getCommandChain()->enqueue($field);
		}

		// Get the primary key
		if($field->primary)
		{
			if (!is_null($this->_primary_key)) {
				throw new KDatabaseException('More than one primary key detected : '.$this->_primary_key.' and '.$field->name);
			}
			$this->_primary_key = $field->name;
		}

		return $this;
	}

	public function removeField($field)
	{
		$field = $this->getField($field);

		if($field instanceof KCommandInterface && !empty($this->_document)){
			$this->_document->getCommandChain()->dequeue($field);
		}

		unset($this->_fields[$field->name]);
		unset($this->_field_map[array_search($name, $this->_field_map)]);

		return $this;
	}

	public function getField($name)
	{
		if($this->addField($name) !== $this){
			return false;
		}

		$field = $this->_fields[$name];

		if (!($field instanceof SDatabaseFieldAbstract)) {
			throw new KDatabaseRowException('Invalid Field type: '.$field.' from '. $this->getIdentifier());
		}

		$this->_fields[$name] = $field;

		return $field;
	}

	public function hasField($field)
	{
		return (
			isset($this->_fields[$field]) &&
				($this->_fields[$field] instanceof KConfig ||
				$this->_fields[$field] instanceof SDatabaseFieldAbstract)
		);
	}

	/**
	 * Return the real field name in db
	 * @param  string $field Unmapped field name
	 * @return string        Mapped field name
	 */
	public function getFieldName($field)
	{
		if (isset($this->_field_map[$field])) {
			return $this->_field_map[$field];
		}

		return $field;
	}

	public function getFields($mapped = false)
	{
		return $this->_fields;
	}

	public function getFieldMap()
	{
		return $this->_field_map;
	}

	public function getUniqueFields()
	{
		if (empty($this->_unique_fields))
		{
			foreach ($this->_fields as $field)
			{
				if ($field->isUnique()) {
					$this->_unique_fields[] = $field;
				}
			}
		}

		return $this->_unique_fields;
	}

	public function getPrimaryKey()
	{
		return $this->_primary_key;
	}
}