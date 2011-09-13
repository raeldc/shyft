<?php

class ComConfigDefault extends KObject implements KObjectIdentifiable
{
    protected $_registry;

    final private function __construct() 
    { 
    	//TODO: Use adapters for setting and getting config? Or not necessary?

        //Created the adapter registry
        self::$_registry = new KLoaderRegistry();
    }

	final private function __clone() { }

    public static function getInstance($config = array())
    {
        static $instance;
        
        if ($instance === NULL) {
            $instance = new self();
        }
        
        return $instance;
    }

	public function getIdentifier()
    {
        return self::$_identifier;
    }

    final public function get($namespace, array $config = array())
    {
        if(!self::$_registry->offsetExists($namespace))
        {
        	$parts = explode('.', $namespace);
        	
        	if (count($parts) < 2)
        		return false;
        	
        	$component = array_shift($parts);
        	$field = array_shift($parts);

            $values = KFactory::get('com:config.model.config')
            	->name($component)
            	->field($field)
            	->getList();

            self::$_registry->offsetSet($namespace, $values);
        }

        return self::$_registry->offsetGet($namespace);
    }
}