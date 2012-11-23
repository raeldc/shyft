<?php

class SDatabaseAdapterMongo extends KObject implements KServiceInstantiatable
{
	protected $_connection;
	protected $_database;
	protected $_fsynced;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		if (is_null($config->connection))
		{
			// TODO: Move this to a specific Mongo adapter
			$connect = 'mongodb://';
			$connect .= (!empty($config->options->username) && !empty($config->options->password)) ? $config->options->username.':'.$config->options->password.'@' : '';
			$connect .= (!empty($config->options->host)) ? $config->options->host : '';
			$connect .= (!empty($config->options->port)) ? ':'. $config->options->port : '';
			$connect .= (!empty($config->database)) ? '/'. $config->database : '';

			$this->setConnection(new Mongo($connect));

			$this->_database = $this->getConnection()->selectDB($config->database);
		}
		else $this->setConnection($config->connection);

        // More sure that data has been inserted/updated
        $this->_synced = $config->fsynced;
	}

	protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'connection'		=> null,
    		'database'			=> '',
    		'fsynced'			=> true,
			'options'	=> array(
    			'host'		=> 'localhost',
    			'username'	=> '',
    			'password'  => '',
    			'port'		=> '27017',
    			'socket'	=> null,
    		)
        ));

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @param 	object	A KServiceInterface object
     * @return KDatabaseTableInterface
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    public function setConnection($resource)
	{
	    if(!($resource instanceof Mongo)) {
	        throw new KDatabaseAdapterException('Not a Mongo connection');
	    }

	    $this->_connection = $resource;
		return $this;
	}

	public function getConnection()
	{
		return $this->_connection;
	}

	public function find(SDatabaseQueryAbstract $query, $mode = KDatabase::FETCH_ROWSET)
	{
		$result = array();

		$collection = $this->_database->selectCollection($query->compile('from'));

		$select = $query->compile('select');
		$where  = $query->compile('where');

		// Get the selected fields if there are any
		if(isset($select->fields)){
			$fields = (array)$select->fields;
		}

		// Make a different query for distincts
		if(isset($select->distinct) && $select->distinct){
			return $this->findDistinct($query);
		}

		switch($mode)
		{
			case KDatabase::FETCH_ROW:
				// Return an empty array if we're not querying anything
				if(empty($where)){
					return array();
				}
				if (!empty($fields)) {
					$result = $collection->findOne($where, $fields);
				}
				else $result = $collection->findOne($where);

			break;

			default:
				if (!empty($fields)) {
					$result  = $collection->find($where, $fields);
				}
				else $result = $collection->find($where);

				$sorting = $query->compile('sort');
				$limit   = $query->compile('limit');

				if (!empty($sorting)) {
					$result = $result->sort($sorting);
				}

				if(isset($limit->limit) && $limit->limit) {
					$result = $result->limit($limit->limit)->skip($limit->offset);
				}

				$result = iterator_to_array($result);
			break;
		}

		return $result;
	}

	public function findDistinct(SDatabaseQueryAbstract $query)
	{
		$select = $query->compile('select');
		$field = end($select->fields);

		$result = $this->_database->command(array(
			'distinct' => $query->compile('from'),
			'key'      => $field,
			'query'    => $query->compile('where')
		));

		$values = array();

		foreach ($result['values'] as $key => $value) {
			$values[] = array($field=>$value);
		}

		return $values;
	}

	public function insert($collection, $data = array())
	{
		$this->_database->selectCollection($collection)->insert($data, array('fsync' => $this->_fsynced));

		return $data;
	}

	public function update($query, $data = array())
	{
		$collection = $this->_database->selectCollection($query->compile('from'));
		$update     = (!empty($data)) ? $query->update($data)->compile('update') : $query->compile('update');
		$where      = $query->compile('where');

		// If there is no update, don't do anything
		if(!$update){
			return 0;
		}

		$collection->update($where, $update, array('fsync' => $this->_fsynced));

		// return affected rows
		$result = $collection->find($where)->count();

		$query->reset();

		return $result;
	}

	public function delete($query)
	{
		$collection = $this->_database->selectCollection($query->compile('from'));
		$where      = $query->compile('where');

		$affected = $collection->find($where)->count();

		$collection->remove($where, array('fsync' => $this->_fsynced));

		$query->reset();

		// return affected rows
		return $affected;
	}

	public function count($query)
	{
		// TODO: Count distinct? Maybe we shouldn't do it

		$count = $this->_database->selectCollection($query->compile('from'))
			->find($query->compile('where'))
			->count();

		return $count;
	}

	public function __call($method, $args)
    {
        if(method_exists($this->_database, $method))
        {
            return call_user_func_array(array($this->_database, $method), $args);
        }

        return parent::__call($method, $args);
    }
}