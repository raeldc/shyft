<?php

/*
 * Model to get a single page. 
 * This is used by the application dispatcher to get the current page.
 */
class ComPagesModelPages extends SModelDocument implements KServiceInstantiatable
{
	protected $_lists  = array();
	protected $_items  = array();
	protected $_totals = array();

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_state = new KConfigState();

		$this->_state
			->insert('enabled', 'boolean', false)
			->insert('page', 'cmd', '', true);
	}

	/**
     * Force creation of a singleton
     *
     * @return ComPagesModelPages
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

    public function getList()
    {
    	// Convert the state into a http query string for caching
    	$state = http_build_query($this->_state->toArray());

    	// Assign a new list if it's not yet in the cache
    	if (!isset($this->_lists[$state])) {
    		$this->_lists[$state] = parent::getList();
    	}

    	return $this->_lists[$state];
    }

    public function getItem()
    {
    	// Convert the state into a http query string for caching
    	$state = http_build_query($this->_state->toArray());

    	// Assign a new list if it's not yet in the cache
    	if (!isset($this->_items[$state])) {
    		$this->_items[$state] = parent::getItem();
    	}

    	return $this->_items[$state];
    }

    public function getTotal()
    {
    	// Convert the state into a http query string for caching
    	$state = http_build_query($this->_state->toArray());

    	// Assign a new list if it's not yet in the cache
    	if (!isset($this->_totals[$state])) {
    		$this->_totals[$state] = parent::getTotal();
    	}

    	return $this->_totals[$state];
    }

	protected function _buildQueryWhere(SDatabaseQueryDocument $query)
	{
		if (!empty($this->_state->page)) {
			$query->where('permalink', '=', $this->_state->page);
		}

		if ($this->_state->enabled) {
			$query->where('enabled', '=', true);
		}
	}
}