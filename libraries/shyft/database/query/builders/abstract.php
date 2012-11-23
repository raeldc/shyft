<?php

abstract class SDatabaseQueryBuilderAbstract extends KObject
{
	/**
	 * The different contexts of the builder
	 * @var ArrayObject
	 */
	protected $_contexts;

	protected $_schema;

	protected $_directives = array();

	protected $_update_operators = array();

	protected $_logic_operators = array();

	protected $_condition_operators = array();

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_schema              = $config->schema;
		$this->_directives          = $config->directives;
		$this->_update_operators    = $config->update_operators;
		$this->_logic_operators     = $config->logic_operators;
		$this->_condition_operators = $config->condition_operators;
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'schema' => '',
			'directives' => array(
				'select'         => '_directiveSelect',
				'selectDistinct' => '_directiveDistinct',
				'update'         => '_directiveUpdate',
				'from'           => '_directiveFrom',
				'where'          => '_directiveWhere',
				'field'          => '_directiveField',
				'sort'           => '_directiveSort',
				'limit'          => '_directiveLimit',
				'offset'         => '_directiveOffset',
			),
			'update_operators' => array(
				'change'         => true
			),
			// Map the operators to function names
			'logic_operators' => array(
				'and'   => '_logicAnd',
				'or'    => '_logicOr',
				'open'  => '_logicOpen',
				'close' => '_logicClose'
			),
			'condition_operators' => array(
				'equalTo'    => true,
				'notEqualTo' => true,
				'lt'         => true,
				'lte'        => true,
				'gt'         => true,
				'gte'        => true,
				'like'       => true,
				'regex'      => true,
				'in'         => true,
			)
		));

		parent::_initialize($config);
	}

	public function execute($command, $arguments = array())
	{
		if($this->conditionExists($command)){
			$this->_processCondition($command, $arguments);
		}
		elseif($this->logicExists($command))
		{
			$logic = $this->_logic_operators->$command;
			$this->$logic();
		}
		elseif($this->updateExists($command))
		{
			$this->_processUpdate($command, $arguments);
		}
		elseif($this->directiveExists($command))
		{
			$method = $this->_directives->$command;

			switch(count($arguments))
	        {
	            case 0 :
	                $this->$method();
	                break;
	            case 1 :
	                $this->$method($arguments[0]);
	                break;
	            case 2:
	                $this->$method($arguments[0], $arguments[1]);
	                break;
	            case 3:
	                $this->$method($arguments[0], $arguments[1], $arguments[2]);
	                break;
	            default:
	                // Resort to using call_user_func_array for many segments
	                call_user_func_array(array($this, $method), $arguments);
	         }
		}
		else throw new KDatabaseException('Unsupported Query Builder Command: '.$command);
	}

	public function updateExists($update)
	{
		return isset($this->_update_operators->$update);
	}

	public function conditionExists($condition)
	{
		return isset($this->_condition_operators->$condition);
	}

	public function logicExists($logic)
	{
		return isset($this->_logic_operators->$logic);
	}

	public function directiveExists($directive)
	{
		return isset($this->_directives->$directive);
	}

	protected function _processUpdate($operator, $arguments = array())
	{
		$update = $this->getContext('update')->current_update;

		// Map the operator to the supported operator
		$operation = $this->_update_operators->$operator;

		if(is_string($operation))
		{
			$this->$operation($update, $arguments);
			$update->open = false;
			return;
		}
		elseif($operation && isset($arguments[0])){
			$update->value = $arguments[0];
		}

		$update->operator = strtolower($operator);
		$update->open = false;
	}

	protected function _processCondition($operator, $arguments = array())
	{
		$condition = $this->getContext('where')->current_condition;

		// Map the operator to the supported operator
		$operation = $this->_condition_operators->$operator;

		if(is_string($operation))
		{
			$this->$operation($condition, $arguments);
			$condition->open = false;
			return;
		}
		elseif($operation && isset($arguments[0])){
			$condition->value = $arguments[0];
		}

		$condition->operator = strtolower($operator);
		$condition->open = false;
	}

	public function getContext($type)
	{
		settype($type, 'string');

		if(!($this->_contexts instanceof ArrayObject)){
			$this->_contexts = new ArrayObject();
		}

		if(!isset($this->_contexts->$type))
		{
			$context                = new ArrayObject();
			$context->open          = true;
			$this->_contexts->$type = $context;
		}
		else $context = $this->_contexts->$type;

		return $context;
	}

	protected function _directiveSelect()
	{
		$context = $this->getContext('select');

		if (!isset($context->fields)) {
			$context->fields = new ArrayObject();
		}

		// Map selected fields first
		$fields = func_get_args();

		foreach($fields as $field)
		{
			if($field = $this->mapField($field)){
				$context->fields->append($field);
			}
		}
	}

	protected function _directiveDistinct($field)
	{
		// Map the selected field
		if($field = $this->mapField($field))
		{
			$context           = $this->getContext('select');
			$context->distinct = true;
			$context->open     = false;
			$context->fields   = array($field);
		}
	}

	protected function _directiveUpdate($field)
	{
		$context = $this->getContext('update');

		if(!isset($context->fields)){
			$context->fields = new ArrayIterator();
		}

		$update = new ArrayObject(array('open' => true));

		if(is_array($field))
		{
			// Map the fields first before putting them here
			$fields = array();
			foreach($field as $current_field => $value)
			{
				if($current_field = $this->mapField($current_field)){
					$fields[$current_field] = $value;
				}
			}

			$update->operator = 'change';
			$update->open     = false;
			$update->value    = $fields;
			$context->fields->append($update);
		}
		else
		{
			if($field = $this->mapField($field))
			{
				$update->field = $field;
				$context->fields->append($update);
			}
		}

		$context->current_update = $update;
	}

	protected function _directiveFrom($from, $alias = null)
	{
		$context = $this->getContext('from');

		if ($alias !== null){
			$key = $alias;
		}
		else $key = $from;

		$alias          = preg_replace('/[^a-zA-Z0-9]/', '', $key);
		$context->table = (string)$from;
		$context->alias = $alias;
	}

	protected function _directiveWhere()
	{
		// Just create a where context;
		$context = $this->getContext('where');
		$context->type = 'where';

		// Make sure that the where context is open and that it has conditions property
		if(!isset($context->conditions)){
			$context->conditions = new ArrayIterator();
		}
	}

	protected function _directiveField($fieldname)
	{
		// If field does not exist in the schema, return;
		if(!($field = $this->mapField($fieldname))){
			return;
		}

		$this->_directiveWhere();
		$condition         = $this->_newCondition();
		$condition->field  = $field;
		$condition->schema = $this->_schema->getField($fieldname);
		$condition->open   = true;

		// Close the current_condition if it's a field type
		$current = $this->getContext('where')->current_condition;

		if(isset($current->field)){
			$current->open = false;
		}
	}

	protected function _directiveSort($fieldname, $direction = 'asc')
	{
		// If field does not exist in the schema, return;
		if(!($field = $this->mapField($fieldname))){
			return;
		}

		$context = $this->getContext('sort');

		if (!isset($context->fields)) {
			$context->fields = new ArrayIterator();
		}

		$current_field            = new ArrayObject();
		$current_field->field     = $field;
		$current_field->schema    = $this->_schema->getField($fieldname);
		$current_field->direction = $direction;

		$context->fields->append($current_field);
	}

	protected function _directiveLimit($limit)
	{
		$this->getContext('limit')->limit = (int)$limit;
	}

	protected function _directiveOffset($offset = 0)
	{
		$this->getContext('limit')->offset = (int)$offset;
	}

	protected function _logicAnd()
	{
		$this->getContext('where')->current_condition->logic = 'and';
	}

	protected function _logicOr()
	{
		$this->getContext('where')->current_condition->logic = 'or';
	}

	protected function _logicOpen()
	{
		$condition = $this->_newCondition();
		$condition->open = true;
		$condition->conditions = new ArrayIterator();
	}

	protected function _logicClose()
	{
		$context = $this->getContext('where');
		$current = $context->current_condition;

		// First figure out the real current condition
		if(isset($current->parent))
		{
			// Close the current field condition
			if(isset($current->field)){
				$current->open = false;
			}

			// Switch the current condition into the current's parent
			$current = $current->parent;
		}

		// Close only if the current condition is an opening statement and if it's open
		if(isset($current->conditions) && $current->open) {
			$current->open = false;
		}

		$context->current_condition = $current;
	}

	/**
	 * Create a new condition and place it where it should be.
	 * @return ArrayObject A newly created condition
	 */
	protected function _newCondition()
	{
		$this->_directiveWhere();
		$context = $this->getContext('where');
		$current = isset($context->current_condition) ? $context->current_condition: null;

		// If current condition has a parent, it means it's within an opening statement
		if($current && isset($current->parent) && (isset($current->field) || !$current->open))
		{
			// If current condition is a field, close it and create a new one
			$parent            = $current->parent;
			$current->open     = false;
			$condition         = new ArrayObject();
			$condition->parent = $parent;

			// Attach the new condition to the parent
			$parent->conditions->append($condition);
		}
		// If current condition is an opening statement. Manipulate sub-conditions.
		elseif($current && isset($current->conditions))
		{
			// If opening statement is open
			if($current->open){
				$parent = $current;
			}
			// If opening statement is closed, set the parent. Parent is the current condition's parent or the where context.
			else $parent = (isset($current->parent)) ? $current->parent: $context;

			// We create a new condition that will be attached to a parent.
			$condition         = new ArrayObject();
			$condition->parent = $parent;
			$parent->conditions->append($condition);
		}
		else
		{
			$condition = new ArrayObject();
			$context->conditions->append($condition);
		}

		$context->current_condition = $condition;

		return $condition;
	}

	public function mapField($key)
	{
		$field = $this->_schema->getField($key);

		if($field){
			$field = $field->field;
		}
		else $field = $key;

		return $field;
	}
}