<?php

/*
 * Model to get a single page. 
 * This is used by the application dispatcher to get the current page.
 */
class ComPagesModelPages extends SModelDefault implements KServiceInstantiatable
{
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