<?php
/**
 * @version		$Id$
 * @package     Koowa_Controller
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract Resource Controller Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Controller
 * @uses        KInflector
 */
abstract class KControllerResource extends KControllerAbstract
{
	/**
	 * View object or identifier (com://APP/COMPONENT.view.NAME.FORMAT)
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

		//Register display as alias for get
		$this->registerActionAlias('display', 'get');

		// Mixin the toolbar
		if($config->dispatch_events) {
            $this->mixin(new KMixinToolbar($config->append(array('mixer' => $this))));
		}
		
		//Made the executable behavior read-only
		if($this->isExecutable()) {
		    $this->getBehavior('executable')->setReadOnly($config->readonly);
		}
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
    	    'model'	     => $this->getIdentifier()->name,
    	    'behaviors'  => array('executable'),
    	    'readonly'   => true,
    		'request' 	 => array('format' => 'html'),
        ))->append(array(
            'view' 		=> $config->request->view ? $config->request->view : $this->getIdentifier()->name
        ));

        parent::_initialize($config);
    }

	/**
	 * Get the view object attached to the controller
	 *
	 * If we are dispatching this controller this function will check if the view folder exists. If not it will throw
     * an exception. This is a security measure to make sure we can only explicitly get data from views the have been
     * physically defined.
	 *
	 * @throws  KControllerException if the view cannot be found. Only when controller is being dispatched.
	 * @return	KViewAbstract
	 *
	 */
	public function getView()
	{
	    if(!$this->_view instanceof KViewAbstract)
		{
		    //Make sure we have a view identifier
		    if(!($this->_view instanceof KServiceIdentifier)) {
		        $this->setView($this->_view);
			}

			//Create the view
			$config = array(
				'model'     => $this->getModel(),
			    'media_url' => KRequest::root().'/media',
			    'base_url'	=> KRequest::url()->getUrl(KHttpUrl::BASE),
                'layout'    => $this->getRequest()->layout
			);

			if($this->isExecutable()) {
			    $config['auto_assign'] = !$this->getBehavior('executable')->isReadOnly();
			}

			$this->_view = $this->getService($this->_view, $config);

			//Make sure the view exists if we are dispatching this controller
            if($this->isDispatched())
            {
                if(!file_exists(dirname($this->_view->getIdentifier()->filepath))) {
                    throw new KControllerException('View : '.$this->_view->getName().' not found', KHttpResponse::NOT_FOUND);
                }
            }
		}

		return $this->_view;
	}

	/**
	 * Method to set a view object attached to the controller
	 *
	 * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @throws	KControllerException	If the identifier is not a view identifier
	 * @return	object	A KViewAbstract object or a KServiceIdentifier object
	 */
	public function setView($view)
	{
		if(!($view instanceof KViewAbstract))
		{
			if(is_string($view) && strpos($view, '.') === false )
		    {
			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('view', $view);
			    $identifier->name	= $this->getRequest()->format;
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
	 * Get the model object attached to the controller
	 *
	 * @return	KModelAbstract
	 */
	public function getModel()
	{
		if(!$this->_model instanceof KModelAbstract)
		{
			//Make sure we have a model identifier
		    if(!($this->_model instanceof KServiceIdentifier)) {
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
	 * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @throws	KControllerException	If the identifier is not a model identifier
	 * @return	object	A KModelAbstract object or a KServiceIdentifier object
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
     * This function will also set the rendered output in the response.
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	string|false 	The rendered output of the view or false if something went wrong
	 */
	protected function _actionGet(KCommandContext $context)
	{
	    $view    = $this->getView();
        $content = $view->display();

        //Set the content in the response
        if($this->isDispatched())
        {
            $context->response
                    ->setContent($content)
                    ->setContentType($view->mimetype);
        }

	    return $content;
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
	    //Check first if we are calling a mixed in method.
	    //This prevents the model being loaded durig object instantiation.
		if(!isset($this->_mixed_methods[$method]))
        {
            //Check if the method is a state property
			$state = $this->getModel()->getState();

			if(isset($state->$method) || in_array($method, array('layout', 'view', 'format')))
			{
				$this->$method = $args[0];

				if($method == 'view') {
                   $this->_view = $args[0];
                }

				return $this;
			}
        }

		return parent::__call($method, $args);
	}
}