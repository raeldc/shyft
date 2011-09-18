<?php

class ComApplicationTemplateContainer extends KObject implements KObjectIdentifiable, KObjectInstantiatable
{
	/**
	 * The containers
	 *
	 * @var	array
	 */
	protected static $_containers = null;

	/**
	 * Constructor
	 *
	 */
	public function __construct(KConfig $config) 
	{ 
		$this->_containers = new ArrayObject();

		// Container is tightly dependent on ComApplicationViewTheme so we have to make sure
		if (!($config->view instanceof ComApplicationViewTheme)) {
			throw new KTemplateException("View must be an instance of ComApplicationViewTheme");
		}

		$this->_view = $config->view;
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'view' => null,
		));

		parent::_initialize($config);
	}
	
	
	/**
     * Get the object identifier
     * 
     * @return  KIdentifier 
     * @see     KObjectIdentifiable
     */
	public function getIdentifier()
	{
		$this->_identifier;
	}

	/**
     * Get the identifier registry object
     * 
     * @return object KFactoryRegistry
     */
    public function getContainers()
    {
        return $this->_containers;
    }

	/**
     * Force creation of a singleton
     *
     * @return ComApplicationTemplateContainer
     */
    public static function getInstance($config = array(), KFactoryInterface $factory = null)
    { 
       // Check if an instance with this identifier already exists or not
        if (!$factory->exists($config->identifier))
        {
            //Create the singleton
            $classname = $config->identifier->classname;
            $instance  = new $classname($config);
            $factory->set($config->identifier, $instance);
        }
        
        return $factory->get($config->identifier);
    }

    /**
     * Append contents into a container 
     *
     * @param  mixed    A value of an or array of values to be appended
     * @return ComApplicationTemplateContainer
     */
    public function append($name, $content, $position = 'bottom')
    {
    	$content = (string)  $content;

    	if (empty($content)) return $this;

    	if(!$this->_containers->offsetExists($name))
		{
			$this->_containers->offsetSet($name, array($content));
			return $this;
		}
		if ($position == 'top') {
			array_unshift($this->get($name), $content);
		}
		else array_push($this->get($name), $content);

		return $this;
    }

    /**
     * Render a container
     *
     * @param  mixed    A value of an or array of values to be appended
     * @return ComApplicationTemplateContainer
     */
    public function render($name, $chrome = 'default')
    {
    	$result = '';

        if($this->_containers->offsetExists($name)) 
        {
            // We expect container to be an array of arrays
            $container = $this->get($name);

            if (!($chrome instanceof KIdentifier)) 
            {
                $identifier = clone $this->_view->getLayout();
                $identifier->path = array('html','chrome');
                $identifier->name = $chrome;
                $chrome = $identifier;
            }

            foreach ($container as $content) 
            {
                if (is_string($content))
                    $content = array('content' => $content);

   				// Render the content using the chrome provided
                $result .= $this->_view
                	->getTemplate()
                    ->loadIdentifier($chrome, $content)
                    ->render();
            }
            
        }

        return $result;
    }

    /**
     * Count contents of a container 
     *
     * @param  mixed    A value of an or array of values to be appended
     * @return ComApplicationTemplateContainer
     */
    public function count($name)
    {
    	return count($this->get($name));
    }

    /**
	 * Get a container
	 *
	 * @param	string|object	The container name
	 * @return	array  			Returns an array of string
	 */
	public function get($name)
	{
		if($this->_containers->offsetExists($name))
		{
			return $this->_containers->offsetGet($name);
		}

		return array();
	}

	/**
     * Retrieve a container
     *
     * @param string 
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

} 