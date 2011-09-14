<?php

class ComApplicationViewTheme extends KViewAbstract
{
	/**
     * Template identifier (theme://APP/template.NAME)
     *
     * @var string|object
     */
    protected $_template;

    /**
     * @var string
     */
    protected $_theme;

    /**
     * @var string
     */
    protected $_layout;

    /**
     * The assigned data
     *
     * @var array
     */
    protected $_data;

    /**
     * The view scripts
     *
     * @var array
     */
    protected $_scripts = array();
    
    /**
     * The view styles
     *
     * @var array
     */
    protected $_styles = array();

    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        // set the auto assign state
        $this->_auto_assign = $config->auto_assign;
        
        //set the data
        $this->_data = KConfig::toData($config->data);
         
        // set the template object
        $this->_template = $config->template;

        // set the theme
        $this->_theme = $config->theme;

        // set the layout
        $this->setLayout($config->layout);
             
        //Set the template filters
        if(!empty($config->template_filters)) {
            $this->getTemplate()->addFilter($config->template_filters);
        }

        //Add alias filter for @component()
        $this->getTemplate()->getFilter('alias')->append(
            array('@component()' => '$this->getView()->getResult();'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );
        
        //Add alias filter for media:// namespace
        $this->getTemplate()->getFilter('alias')->append(
            array('css://' => $config->css_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );

        //Add alias filter for media:// namespace
        $this->getTemplate()->getFilter('alias')->append(
            array('js://' => $config->js_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );

        //Add alias filter for media:// namespace
        $this->getTemplate()->getFilter('alias')->append(
            array('image://' => $config->image_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );
        
        //Add alias filter for base:// namespace
        $this->getTemplate()->getFilter('alias')->append(
            array('base://' => $config->base_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        //Clone the identifier
        $identifier = clone $this->_identifier;
        
        $config->append(array(
            'data'			   => array(),
            'template'         => $this->getName(),
            'layout'           => 'default',
            'theme'			   => 'bootstrap',
            'auto_assign'      => true,
            'replace_filters'  => false,
            'base_url'         => KRequest::base(),
            'root_url'         => KRequest::root(), 
            'themes_url'        => KRequest::root().'/themes' 
        ));

        if(!empty($config->theme)) {
            $config->append(array(
                'css_url'          => $config->themes_url.'/'.$config->theme.'/css',
                'js_url'           => $config->themes_url.'/'.$config->theme.'/js',
                'image_url'        => $config->themes_url.'/'.$config->theme.'/images',
            ));
        }

        if ($config->replace_filters) 
        {
            $config->template_filters = $config->template_filters;
        }
        else $config->append(array(
            'template_filters' => array('shorttag', 'alias', 'variable', 'template')
        ));
        
        parent::_initialize($config);
    }

    /**
     * Set a view properties
     *
     * @param   string  The property name.
     * @param   mixed   The property value.
     */
    public function __set($property, $value)
    {
        $this->_data[$property] = $value;
    }
    
    /**
     * Get a view property
     *
     * @param   string  The property name.
     * @return  string  The property value.
     */
    public function __get($property)
    {
        $result = null;
        if(isset($this->_data[$property])) {
            $result = $this->_data[$property];
        } 
        
        return $result;
    }

    /**
    * Assigns variables to the view script via differing strategies.
    *
    * This method is overloaded; you can assign all the properties of
    * an object, an associative array, or a single value by name.
    *
    * You are not allowed to set variables that begin with an underscore;
    * these are either private properties for KView or private variables
    * within the template script itself.
    *
    * <code>
    * $view = new KViewDefault();
    *
    * // assign directly
    * $view->var1 = 'something';
    * $view->var2 = 'else';
    *
    * // assign by name and value
    * $view->assign('var1', 'something');
    * $view->assign('var2', 'else');
    *
    * // assign by assoc-array
    * $ary = array('var1' => 'something', 'var2' => 'else');
    * $view->assign($obj);
    *
    * // assign by object
    * $obj = new stdClass;
    * $obj->var1 = 'something';
    * $obj->var2 = 'else';
    * $view->assign($obj);
    *
    * </code>
    *
    * @return KViewAbstract
    */
    public function assign()
    {
        // get the arguments; there may be 1 or 2.
        $arg0 = @func_get_arg(0);
        $arg1 = @func_get_arg(1);

        // assign by object or array
        if (is_object($arg0) || is_array($arg0)) {
            $this->set($arg0);
        } 
        
        // assign by string name and mixed value.
        elseif (is_string($arg0) && substr($arg0, 0, 1) != '_' && func_num_args() > 1) {
            $this->set($arg0, $arg1);
        }

        return $this;
    }
    
    public function setResult($result)
    {
        $this->_result = $result;

        return $this;
    }

    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Return the views output
     *
     * @return string 	The output of the view
     */
    public function display()
    {
        if(empty($this->output))
		{
		    $this->output = $this->getTemplate()
                                 ->loadIdentifier($this->_layout, $this->_data)
                                 ->render();
		}
                        
        return parent::display();
    }
    
	/**
     * Sets the layout name
     *
     * @param    string  The template name.
     * @return   KViewAbstract
     */
    public function setLayout($layout)
    {
        if(is_null($this->_theme) || is_null($layout)) {
            return $this;
        }

        if((is_string($layout) && strpos($layout, '.') === false)) 
		{
            $identifier = clone $this->_identifier;
            $identifier->type = 'theme';
            $identifier->package = $this->_theme;
            $identifier->path = array();
            $identifier->name = $layout;
	    }
		else $identifier = KIdentifier::identify($layout);
        
        $this->_layout = $identifier;
        return $this;
    }
    
	/**
     * Get the layout.
     *
     * @return string The layout name
     */
    public function getLayout()
    {
        if(!($this->_layout instanceof KIdentifier))
        {
            $this->setLayout($this->_layout);
        }

        return $this->_layout;
    }
    
    /**
     * Get the identifier for the template with the same name
     *
     * @return  KIdentifierInterface
     */
    public function getTemplate()
    {
        if(!($this->_template instanceof KTemplateAbstract))
        { 
            //Make sure we have a template identifier
            if(!($this->_template instanceof KIdentifier)) {
                $this->setTemplate($this->_template);
            }
              
            $options = array(
            	'view' => $this
            );

            $this->_template = KFactory::get($this->_template, $options);
        }
        
        return $this->_template;
    }
    
    /**
     * Method to set a template object attached to the view
     *
     * @param   mixed   An object that implements KObjectIdentifiable, an object that 
     *                  implements KIdentifierInterface or valid identifier string
     * @throws  KDatabaseRowsetException    If the identifier is not a table identifier
     * @return  KViewAbstract
     */
    public function setTemplate($template)
    {
        if(!($template instanceof KTemplateAbstract))
        {
            if(is_string($template) && strpos($template, '.') === false ) 
            {
                $identifier = clone $this->_identifier; 
                $identifier->path = array('template');
                $identifier->name = $template;
            }
            else $identifier = KIdentifier::identify($template);
            
            if($identifier->path[0] != 'template') {
                throw new KViewException('Identifier: '.$identifier.' is not a template identifier');
            }
        
            $template = $identifier;
        } 
        
        $this->_template = $template;
            
        return $this;
    }
    
    /**
     * Execute and return the views output
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->display();
    }
    
    /**
     * Supports a simple form of Fluent Interfaces. Allows you to assign variables to the view 
     * by using the variable name as the method name. If the method name is a setter method the 
     * setter will be called instead.
     *
     * For example : $view->layout('foo')->title('name')->display().
     *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     * @return  KViewAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args) 
    { 
        //If one argument is passed we assume a setter method is being called 
        if(count($args) == 1) 
        { 
            if(method_exists($this, 'set'.ucfirst($method))) { 
                return $this->{'set'.ucfirst($method)}($args[0]); 
            } else { 
                return $this->set($method, $args[0]); 
            } 
        } 
        
        return parent::__call($method, $args); 
    } 
}