<?php

class SDatabaseQueryCompilerMongo extends KObject implements KServiceInstantiatable
{
	protected $_supported_contexts = array('where', 'select', 'update', 'from', 'sort', 'limit');

    protected $_update_operators = array(
        'change'       => '$set',
        'add'          => '$inc',
        'unset'        => '$unset',
        'append'       => '$push',
        'appendAll'    => '$pushAll',
        'merge'        => '$addToSet',
        'extractfirst' => '$extractFirst',
        'extractlast'  => '$extractLast',
        'deletefield'  => '$unset',
        'pull'         => '$pull',
    );

	protected $_condition_operators = array(
        'notequalto' => '$ne',
        'exists'     => '$exists',
		'missing'    => '$exists',
		'modulo'     => '$mod',
		'size'       => '$size',
		'type'       => '$type',
		'hasall'     => '$all',
		'notin'      => '$nin',
		'lt'         => '$lt',
		'lte'        => '$lte',
		'gt'         => '$gt',
		'gte'        => '$gte',
		'in'         => '$in',
		'regex'      => true,
	);

	/**
     * Force creation of a singleton
     *
     * @param 	object 	An optional ArrayObject object with configuration options
     * @param 	object	A KServiceInterface object
     * @return SDatabaseQueryCompilerMongo
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    public function compile(ArrayObject $context)
    {
    	if (!in_array($context->type, $this->_supported_contexts)) {
    		throw new KDatabaseExeption('Compiler does not support context: '.$context->type);
    	}

    	$method = '_compile'.ucfirst($context->type);

    	return $this->$method($context);
    }

    protected function _compileSelect(ArrayObject $context)
    {
        return $context;
    }

    protected function _compileFrom(ArrayObject $context)
    {
        return $context->table;
    }

    protected function _compileWhere(ArrayObject $context)
    {
    	$compilation = array();

    	// If the context only has one condition
        if(isset($context->conditions))
        {
        	if(($conditions = count($context->conditions)) === 1){
        		$compilation = $this->_compileCondition($context);
        	}
        	elseif($conditions > 1){
        		$compilation = $this->_compileConditions($context->conditions);
        	}
        }

    	return $compilation;
    }

    protected function _compileUpdate(ArrayObject $context)
    {
        if(!isset($context->fields)){
            return false;
        }

        $compilation = array();

        foreach($context->fields as $field)
        {
            if(isset($field->operator) && isset($this->_update_operators[$field->operator]))
            {
                $operator = $this->_update_operators[$field->operator];

                if ($field->value instanceof ArrayObject && $operator == '$set')
                {
                    $operand = array();
                    foreach($field->value as $key => $value){
                        $operand[$key] = $value;
                    }
                    $compilation = array_merge_recursive($compilation, array(
                        '$set' => $operand
                    ));
                }
                else
                {
                    $value     = $field->value;
                    $field     = isset($field->field) ? $field->field : false;
                    $operation = ($field) ? array( $field => $value) : $value;

                    switch($operator)
                    {
                        case '$addToSet':
                            if($value instanceof ArrayObject)
                            {
                                $value = array('$each' => $value->toArray());
                            }
                        break;
                        case '$extractFirst':
                            $operator = '$pop';
                            $value = -1;
                        break;
                        case '$extractLast':
                            $operator = '$pop';
                            $value = 1;
                        break;
                        case '$unset':
                            $value = 1;
                        break;
                    }

                    $compilation = array_merge_recursive($compilation, array($operator => $operation));
                }
            }
        }

        return $compilation;
    }

    protected function _compileCondition($condition)
    {
    	$compilation = array();

		// If the condition has conditions
		if(isset($condition->conditions))
		{
            $logic = isset($condition->logic) ? $condition->logic : null;
			$compilation = $this->_compileConditions($condition->conditions);
		}
		else
		{
            if(isset($condition->schema) && $condition->schema instanceof SDatabaseFieldAbstract)
            {
                if(in_array($condition->operator, array('in', 'hasall', 'notin')))
                {
                    $value = array();
                    foreach($condition->value as $current_value)
                    {
                        $value[] = $condition->schema->processForQuery($current_value);
                    }
                }
                else $value = $condition->schema->processForQuery($condition->value);
            }
            else $value = $condition->value;

			if(isset($this->_condition_operators[$condition->operator]))
            {
                if($condition->operator == 'regex'){
                    $operand = new MongoRegex($value);
                }
				else $operand = array($this->_condition_operators[$condition->operator] => $value);
			}
			else $operand = $value;

			$compilation = array($condition->field => $operand);
		}

		return $compilation;
    }

    protected function _compileConditions(ArrayIterator $conditions, $logic = 'and')
    {
    	$compilation = array();

    	foreach($conditions as $condition)
    	{
    		// If the condition has conditions

			if(isset($condition->conditions))
			{
				// If the context has only one condition
		    	if(($conditions = count($condition->conditions)) === 1){
		    		$compilation[] = $this->_compileCondition($condition);
		    	}
		    	elseif($conditions > 1){
		    		$compilation[] = $this->_compileConditions($condition->conditions, $condition->logic);
		    	}
			}
			else $compilation[] = $this->_compileCondition($condition);
    	}

    	if($logic === 'or'){
    		return array('$or' => $compilation);
    	}

    	return array('$and' => $compilation);
    }

    protected function _compileSort(ArrayObject $context)
    {
        $sorting = array();

        if (isset($context->fields))
        {
            foreach($context->fields as $field)
            {
                $sorting[$field->field] = (strtolower($field->direction) == 'asc') ? 1 : -1;
            }
        }

        return $sorting;
    }

    protected function _compileLimit(ArrayObject $context)
    {
        return $context;
    }
}