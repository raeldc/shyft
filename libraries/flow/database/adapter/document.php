<?php

class FlowDatabaseAdapterDocument extends KObject implements KObjectIdentifiable 
{
	protected $_connection;
	protected $_database;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		if (is_null($config->connection)) 
		{
			// TODO: Move this to a specific Mongo adapter
			$connect = 'mongodb://';
			$connect .= (!empty($config->options->username) && !empty($config->options->password)) ? $config->options->username.':'.$config->options->password.'@' : '';
			$connect .= (!empty($config->options->host)) ? $config->options->host : '';

			$this->setConnection(new Mongo($connect));

			$this->_database = $this->getConnection()->selectDB($config->database);
		}
		else $this->setConnection($config->connection);

		// Mixin a command chain
        $this->mixin(new KMixinCommandchain($config->append(array('mixer' => $this))));
	}

	public function getIdentifier()
	{
		return $this->_identifier;
	}

	protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'connection'		=> null,
    		'database'			=> 'flowku',
			'options'	=> array(
    			'host'		=> 'localhost', 
    			'username'	=> null,
    			'password'  => null,
    			'port'		=> null,
    			'socket'	=> null
    		)
        ));
         
        parent::_initialize($config);
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

	public function __call($method, $args)
    {
        if(method_exists($this->_database, $method))
        {
            return call_user_func_array(array($this->_database, $method), $args);
        }

        return parent::__call($method, $args);
    }
}