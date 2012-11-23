<?php

abstract class SDatabaseFieldAbstract extends KObject
{
	/**
	 * @var  string  a pretty name for the field
	 */
	protected $_label;

	/**
	 * @var  string  the field's name in the form
	 */
	protected $_name;

	/**
	 * @var  string  the field's real name in the table or document
	 */
	protected $_field;

	/**
	 * @var  boolean  whether or not the field should be unique
	 */
	protected $_unique = false;

	/**
	* @var  boolean  a primary key field.
	*/
	protected $_primary = false;

	/**
	* @var  boolean  the column is present in the database table. Default: true
	*/
	protected $_indb = true;

	/**
	* @var  string  filters are called whenever data is set on the field
	*/
	protected $_filter = null;

	/**
	* @var  mixed  default value
	*/
	protected $_default = null;

	/**
	* @var  mixed  real value
	*/
	protected $_value = null;

	protected $_enable_processing = true;

	protected $_validation = null;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_label      = (string)$config->label;
		$this->_name       = (string)$config->name;
		$this->_field 	   = (string)$config->field;
		$this->_unique     = (bool)$config->unique;
		$this->_primary    = (bool)$config->primary;
		$this->_indb       = (bool)$config->indb;
		$this->_filter     = (string)$config->filter;
		$this->_default    = $config->default;
		$this->_validate = $config->validate;
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'name'       => $this->getIdentifier()->name,
			'primary'    => false,
			'indb'       => true,
			'filter'     => $this->getIdentifier()->name,
			'default'    => null,
		))->append(array(
			'field'  	 => $config->name,
			'label'      => ucfirst($config->name),
			'unique'     => $config->primary,
			'validate' => $config->validate,
		));

		parent::_initialize($config);
	}

	final public function processValue($value, KDatabaseRowAbstract $row)
	{
		if($this->_enable_processing){
			return $this->_process($value, $row);
		}

		return $value;
	}

	final public function processToArray($value, KDatabaseRowAbstract $row)
	{
		if($this->_enable_processing){
			return $this->_toArray($value, $row);
		}

		return $value;
	}

	final public function processForQuery($value)
	{
		if($this->_enable_processing){
			return $this->_forQuery($value);
		}

		return $value;
	}

	final public function filterValue($value)
	{
		return $this->getService('koowa:filter.factory')->instantiate($this->filter)->sanitize($value);
	}

	protected function _process($value, KDatabaseRowAbstract $row)
	{
		return $value;
	}

	protected function _toArray($value, KDatabaseRowAbstract $row)
	{
		return $value;
	}

	protected function _forQuery($value)
	{
		return $value;
	}

	public function enableProcessing()
	{
		$this->_enable_processing = true;
	}

	public function disableProcessing()
	{
		$this->_enable_processing = false;
	}

	public function __call($method, array $arguments)
	{
		// If the method is of the form is[Bahavior] handle it.
        $parts = KInflector::explode($method);

        if($parts[0] == 'is' && isset($parts[1]))
        {
        	$property = '_'.$parts[1];
            if (isset($this->$property) && is_bool($this->$property)) {
				return $this->$property;
			}
        }

        return false;
	}

	public function __get($property)
	{
		$key = '_'.$property;
		return $this->$key;
	}
}