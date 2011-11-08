<?php

class ComApplicationTemplateContainer extends KObject implements KServiceInstantiatable
{
	/**
	 * The containers
	 *
	 * @var	array
	 */
	protected static $_containers = null;

    /**
     * The view
     *
     * @var object
     */
    protected $_view;

	/**
	 * Constructor
	 *
	 */
	public function __construct(KConfig $config) 
	{
        parent::__construct($config);

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
     * Get the identifier registry object
     * 
     * @return object ArrayObject
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

    /**
     * Append contents into a container 
     *
     * @param  mixed    A value of an or array of values to be appended
     * @return ComApplicationTemplateContainer
     */
    public function append($name, $content, $position = 'append')
    {
    	$content = (string)  $content;

    	if (empty($content)) return $this;

    	if(!$this->_containers->offsetExists($name))
		{
			$this->_containers->offsetSet($name, new ContainerArrayObject(array($content)));
			return $this;
		}
		if ($position == 'prepend') {
			$this->get($name)->prepend($content);
		}
		else $this->get($name)->append($content);

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
                $content = (is_string($content)) ? array('content' => $content) : $content;

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

		return new ArrayObject();
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

class ContainerArrayObject extends ArrayObject 
{
    public function prepend($value) 
    {
        $array = (array)$this;
        array_unshift($array, $value);
        $this->exchangeArray($array);
    }
}