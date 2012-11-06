<?php

class ComApplicationViewTheme extends KViewTemplate implements KServiceInstantiatable
{
    /**
     * @var string
     */
    protected $_theme;

    /**
     * @var string
     */
    protected $_layout;

    /**
     * Containers
     *
     * @var ComApplicatonTemplateContainer
     */
    protected $_container;

    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // Set the theme filters instead of template filters.
        if(!empty($config->theme_filters)) {
            $this->getTemplate()->addFilter($config->theme_filters);
        }

        $this->getTemplate()->getFilter('alias')->append(
            array('@container(' => '$this->getView()->getContainer()->render('), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );

        $this->getTemplate()->getFilter('alias')->append(
            array('@contains(' => '$this->getView()->getContainer()->count('), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );

        $this->getTemplate()->getFilter('alias')->append(
            array('css://' => $config->css_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );

        $this->getTemplate()->getFilter('alias')->append(
            array('js://' => $config->js_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );

        $this->getTemplate()->getFilter('alias')->append(
            array('image://' => $config->image_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );

        $this->assign('baseurl' , $config->base_url)
             ->assign('mediaurl', $config->media_url);
        
        $this->getTemplate()->getFilter('alias')->append(
            array('media://' => $config->media_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );
        
        $this->getTemplate()->getFilter('alias')->append(
            array('base://' => $config->base_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );
       
        $this->_theme = $config->theme;

        $this->setLayout($config->layout);

        $this->_container = $config->container;
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
        $identifier = clone $this->getIdentifier();
        
        $config->append(array(
            'template'      => $this->getName(),
            'layout'        => 'default',
            'theme'         => 'bootstrap',
            'container'     => null,
            'themes_url'    => KRequest::root().'/themes',
            'theme_filters' => array('shorttag', 'alias', 'variable', 'template'),
        ));

        // Empty the filters because we don't want to use the default set of filters
        $config->template_filters = null;

        if(!empty($config->theme)) 
        {
            $config->append(array(
                'css_url'   => $config->themes_url.'/'.$config->theme.'/css',
                'js_url'    => $config->themes_url.'/'.$config->theme.'/js',
                'image_url' => $config->themes_url.'/'.$config->theme.'/images',
            ));
        }

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton
     *
     * @return ComApplicationViewTheme
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
            $identifier          = clone $this->getIdentifier();
            $identifier->type    = 'theme';
            $identifier->package = $this->_theme;
            $identifier->path    = array();
            $identifier->name    = $layout;
	    }
		else $identifier = $this->getIdentifier($layout);
        
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

    public function getContainer()
    {
        if(!($this->_container instanceof ComApplicationTemplateContainer))
        {
            if(is_string($this->_container) && strpos($this->_container, '.') === true)
            {
                $identifier = $this->getIdentifier($this->_container);   
            }
            else $identifier = $this->getIdentifier('com://site/application.template.container');

            $config = array(
                'view' => $this
            );

            $this->_container = $this->getService($identifier, $config);

            // Container is tightly dependent on ComApplicatonTemplateContainer so we have to make sure
            if (!($this->_container instanceof ComApplicationTemplateContainer)) {
               throw new KTemplateException("View must be an instance of ComApplicationTemplateContainer");
            }
        }

        return $this->_container;
    }

    public function display()
    {
        if(empty($this->output)) {
            $this->getContainer()->append('page', $this->component);
        }

        return parent::display();
    }
}