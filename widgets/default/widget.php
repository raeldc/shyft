<?php

abstract class WidgetDefaultWidget extends KObject
{
	/**
	 * View object or identifier (widget://APP/COMPONENT.view.NAME.FORMAT)
	 *
	 * @var	string|object
	 */
	protected $_view;
	
	/**
	 * Model object or identifier (com://APP/COMPONENT.model.NAME)
	 *
	 * @var	string|object
	 */
	protected $_model;

	/**
	 * The request information
	 *
	 * @var array
	 */
	protected $_request = null;
	
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

	    // Set the model identifier
	    $this->_model = $config->model;
		
		// Set the view identifier
		$this->_view = $config->view;
	}

	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    	    'model'	     => $this->getIdentifier()package,
        	'view'	     => $this->getIdentifier()package,
        ));

        parent::_initialize($config);
        
        //Force the view to the information found in the request
        if(isset($config->request->view)) {
            $config->view = $config->request->view;
        }
    }

    /**
	 * Gets the service identifier.
	 *
	 * @return	KServiceIdentifier
	 * @see 	KObjectServiceable
	 */
	final public function getIdentifier($identifier = null)
	{
		if(isset($identifier)) {
		    $result = $this->__service_container->getIdentifier($identifier);
		} else {
		    $result = $this->__service_identifier; 
		}
	    
	    return $result;
	}

    /**
	 * Get the request information
	 *
	 * @return KConfig	A KConfig object with request information
	 */
	public function getRequest()
	{
		return $this->_request;
	}
    
	/**
	 * Get the view object attached to the controller
	 * 
	 * This function will check if the view folder exists. If not it will throw
	 * an exception. This is a security measure to make sure we can only explicitly
	 * get data from views the have been physically defined. 
	 *
	 * @throws  KControllerException if the view cannot be found.
	 * @return	KViewAbstract
	 *  
	 */
	public function getView()
	{
	    if(!$this->_view instanceof KViewAbstract)
		{	   
		    //Make sure we have a view identifier
		    if(!($this->_view instanceof KIdentifier)) {
		        $this->setView($this->_view);
			}

			//Create the view
			$config = array(
			    'model'  => $this->getModel(),
        	);

			$this->_view = $this->getService($this->_view, $config);

			//Set the layout
			if(isset($this->_request->layout)) {
        	    $this->_view->setLayout($this->_request->layout);
        	}

        	//Make sure the view exists
		    if(!file_exists(dirname($this->_view->getIdentifier()->filepath))) {
		        throw new KControllerException('View : '.$this->_view->getName().' not found', KHttpResponse::NOT_FOUND);
		    }
		}
		
		return $this->_view;
	}

	/**
	 * Method to set a view object attached to the controller
	 *
	 * @param	mixed	An object that implements KObject, an object that
	 *                  implements KIdentifierInterface or valid identifier string
	 * @throws	KControllerException	If the identifier is not a view identifier
	 * @return	object	A KViewAbstract object or a KIdentifier object
	 */
	public function setView($view)
	{
		if(!($view instanceof KViewAbstract))
		{
			if(is_string($view) && strpos($view, '.') === false ) 
		    {
			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('view', $view);
			    $identifier->name	= 'html';
			}
			else $identifier = $this->getIdentifier($view);

			if($identifier->path[0] != 'view') {
				throw new KControllerException('Identifier: '.$identifier.' is not a view identifier');
			}

			$view = $identifier;
		}
		
		$this->_view = $view;
		
		return $this->_view;
	}
	
	/**
	 * Get the model object attached to the contoller
	 *
	 * @return	KModelAbstract
	 */
	public function getModel()
	{
		if(!$this->_model instanceof KModelAbstract) 
		{
			//Make sure we have a model identifier
		    if(!($this->_model instanceof KIdentifier)) {
		        $this->setModel($this->_model);
			}

		    //@TODO : Pass the state to the model using the options
		    $options = array(
				'state' => $this->getRequest()
            );
		    
		    $this->_model = $this->getService($this->_model)->set($this->getRequest());
		}

		return $this->_model;
	}

	/**
	 * Method to set a model object attached to the controller
	 *
	 * @param	mixed	An object that implements KObject, an object that
	 *                  implements KIdentifierInterface or valid identifier string
	 * @throws	KControllerException	If the identifier is not a model identifier
	 * @return	object	A KModelAbstract object or a KIdentifier object
	 */
	public function setModel($model)
	{
		if(!($model instanceof KModelAbstract))
		{
	        if(is_string($model) && strpos($model, '.') === false ) 
		    {
			    // Model names are always plural
			    if(KInflector::isSingular($model)) {
				    $model = KInflector::pluralize($model);
			    } 
		        
			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('model');
			    $identifier->name	= $model;
			}
			else $identifier = $this->getIdentifier($model);

			if($identifier->path[0] != 'model') {
				throw new KControllerException('Identifier: '.$identifier.' is not a model identifier');
			}

			$model = $identifier;
		}
		
		$this->_model = $model;
		
		return $this->_model;
	}
	
	/**
	 * Specialised display function.
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	string|false 	The rendered output of the view or false if something went wrong
	 */
	public function display()
	{
	    return $this->getView()->display();
	}
	
	/**
     * Set a request properties
     * 
     * This function also pushes any request changes into the model
     *
     * @param  	string 	The property name.
     * @param 	mixed 	The property value.
     */
 	public function __set($property, $value)
    {
    	parent::__set($property, $value);
    	
    	//Prevent state changes through the parents constructor 
    	if($this->_model instanceof KModelAbstract) {
    	    $this->getModel()->set($property, $value);
    	}
  	}
	
	/**
	 * Supports a simple form Fluent Interfaces. Allows you to set the request 
	 * properties by using the request property name as the method name.
	 *
	 * For example : $controller->view('name')->limit(10)->browse();
	 *
	 * @param	string	Method name
	 * @param	array	Array containing all the arguments for the original call
	 * @return	KControllerBread
	 *
	 * @see http://martinfowler.com/bliki/FluentInterface.html
	 */
	public function __call($method, $args)
	{
	    //Check if the method is a state property
		$state = $this->getModel()->getState();

		if(isset($state->$method) || in_array($method, array('layout', 'view', 'format'))) 
		{
			$this->$method = $args[0];
			return $this;
		}

		return parent::__call($method, $args);
	}
}