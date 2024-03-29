<?php
/**
 * @version        $Id$
 * @package        Koowa_Template
 * @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         http://www.nooku.org
 */

/**
 * Abstract Template class
 *
 * @author        Johan Janssens <johan@nooku.org>
 * @package    Koowa_Template
 */
abstract class KTemplateAbstract extends KObject implements KTemplateInterface
{
    /**
     * The template path
     *
     * @var string
     */
    protected $_file;

    /**
     * The template data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * The template contents
     *
     * @var string
     */
    protected $_contents = '';

    /**
     * The set of template filters for templates
     *
     * @var array
     */
    protected $_filters = array();

    /**
     * View object or identifier (com://APP/COMPONENT.view.NAME.FORMAT)
     *
     * @var    string|object
     */
    protected $_view;

    /**
     * The template stack object
     *
     * @var    KTemplateStack
     */
    protected $_stack;

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the contructor private
     *
     * @param     object     An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // Set the view indentifier
        $this->_view = $config->view;

        // Set the template stack object
        $this->_stack = $config->stack;

        //Register the template stream wrapper
        KTemplateStream::register();

        // Mixin a command chain
        $this->mixin(new KMixinCommand($config->append(array('mixer' => $this))));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param     object     An optional KConfig object with configuration options.
     * @return     void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'stack'            => $this->getService('koowa:template.stack'),
            'view'             => null,
            'command_chain'    => $this->getService('koowa:command.chain'),
            'dispatch_events'  => false,
            'enable_callbacks' => false,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the template file identifier
     *
     * @return    KServiceIdentifier
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Get the template data
     *
     * @return    mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get the template object stack
     *
     * @return     KTemplateStack
     */
    public function getStack()
    {
        return $this->_stack;
    }

    /**
     * Get the view object attached to the template
     *
     * @return    KViewAbstract
     */
    public function getView()
    {
        if (!$this->_view instanceof KViewAbstract)
        {
            //Make sure we have a view identifier
            if (!($this->_view instanceof KServiceIdentifier)) {
                $this->setView($this->_view);
            }

            $this->_view = $this->getService($this->_view);
        }

        return $this->_view;
    }

    /**
     * Method to set a view object attached to the template
     *
     * @param mixed  An object that implements KObjectServiceable, KServiceIdentifier object
     *               or valid identifier string
     * @throws KDatabaseRowsetException    If the identifier is not a view identifier
     * @return KControllerAbstract
     */
    public function setView($view)
    {
        if (!($view instanceof KViewAbstract))
        {
            if (is_string($view) && strpos($view, '.') === false)
            {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('view', $view);
                $identifier->name = 'html';
            }
            else $identifier = $this->getIdentifier($view);

            if ($identifier->path[0] != 'view') {
                throw new KTemplateException('Identifier: ' . $identifier . ' is not a view identifier');
            }

            $view = $identifier;
        }

        $this->_view = $view;

        return $this;
    }

    /**
     * Load a template by identifier
     *
     * This functions only accepts full identifiers of the format
     * -  com:[//application/]component.view.[.path].name
     *
     * @param   string   The template identifier
     * @param   array    An associative array of data to be extracted in local template scope
     * @param   boolean  If TRUE evaluate the data using a tmpl:// stream. Default TRUE.
     * @return KTemplateAbstract
     */
    public function loadIdentifier($template, $data = array(), $evaluate = true)
    {
        //Find the template
        $identifier = $this->getIdentifier($template);
        $file = $this->findFile($identifier->filepath);

        //Store the identifier
        $this->_file = $identifier;

        if ($identifier->filepath === false) {
            throw new KTemplateException('Template "' . $identifier->name . '" not found');
        }

        // Load the file
        $this->loadFile($file, $data, $evaluate);

        return $this;
    }

    /**
     * Load a template by path
     *
     * @param   string   The template path
     * @param   array    An associative array of data to be extracted in local template scope
     * @param   boolean  If TRUE evaluate the data using a tmpl:// stream. Default TRUE.
     * @return KTemplateAbstract
     */
    public function loadFile($file, $data = array(), $evaluate = true)
    {
        //Get the file contents
        $contents = file_get_contents($file);

        //Load the contents
        $this->loadString($contents, $data, $evaluate);

        return $this;
    }

    /**
     * Load a template from a string
     *
     * @param  string   The template contents
     * @param  array    An associative array of data to be extracted in local template scope
     * @param  boolean  If TRUE evaluate the data using a tmpl stream. Default TRUE.
     * @return KTemplateAbstract
     */
    public function loadString($string, $data = array(), $evaluate = true)
    {
        $this->_contents = $string;

        // Merge the data
        $this->_data = array_merge((array)$this->_data, $data);

        // Process the data
        if ($evaluate == true) {
            $this->__sandbox();
        }

        return $this;
    }

    /**
     * Render the template
     *
     * This function passes the template through write filter chain and returns the
     * result.
     *
     * @return string    The rendered data
     */
    public function render()
    {
        $context = $this->getCommandContext();
        $context->data = $this->_contents;

        $result = $this->getCommandChain()->run(KTemplateFilter::MODE_WRITE, $context);

        return $context->data;
    }

    /**
     * Parse the template
     *
     * This function passes the template through read filter chain and returns the
     * result.
     *
     * @return string    The parsed data
     */
    public function parse()
    {
        $context = $this->getCommandContext();
        $context->data = $this->_contents;

        $result = $this->getCommandChain()->run(KTemplateFilter::MODE_READ, $context);

        return $context->data;
    }

    /**
     * Get a filter by identifier
     *
     * @return KTemplateFilterInterface
     */
    public function getFilter($filter, $config = array())
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($filter) && strpos($filter, '.') === false)
        {
            $identifier = clone $this->getIdentifier();
            $identifier->path = array('template', 'filter');
            $identifier->name = $filter;
        }
        else $identifier = $this->getIdentifier($filter);

        if (!isset($this->_filters[$identifier->name]))
        {
            $filter = $this->getService($identifier, array_merge($config, array('template' => $this)));

            if (!($filter instanceof KTemplateFilterInterface)) {
                throw new KTemplateException("Template filter $identifier does not implement KTemplateFilterInterface");
            }

            $this->_filters[$filter->getIdentifier()->name] = $filter;
        }
        else $filter = $this->_filters[$identifier->name];

        return $filter;
    }

    /**
     * Attach one or more filters for template transformation
     *
     * @param array     Array of one or more behaviors to add.
     * @return KTemplate
     */
    public function attachFilter($filters)
    {
        $filters = (array)KConfig::unbox($filters);

        foreach ($filters as $filter)
        {
            if (!($filter instanceof KTemplateFilterInterface)) {
                $filter = $this->getFilter($filter);
            }

            //Enqueue the filter in the command chain
            $this->getCommandChain()->enqueue($filter);
        }

        return $this;
    }

    /**
     * Get a template helper
     *
     * @param    mixed    KServiceIdentifierInterface
     * @param    array    An optional associative array of configuration settings
     * @return     KTemplateHelperInterface
     */
    public function getHelper($helper, $config = array())
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($helper) && strpos($helper, '.') === false)
        {
            $identifier = clone $this->getIdentifier();
            $identifier->path = array('template', 'helper');
            $identifier->name = $helper;
        }
        else $identifier = $this->getIdentifier($helper);

        //Create the template helper
        $helper = $this->getService($identifier, array_merge($config, array('template' => $this)));

        //Check the helper interface
        if (!($helper instanceof KTemplateHelperInterface)) {
            throw new KTemplateHelperException("Template helper $identifier does not implement KTemplateHelperInterface");
        }

        return $helper;
    }

    /**
     * Load a template helper
     *
     * This functions accepts a partial identifier, in the form of helper.function. If a partial identifier is passed a
     * full identifier will be created using the template identifier.
     *
     * @param    string    Name of the helper, dot separated including the helper function to call
     * @param    array    An optional associative array of configuration settings
     * @return     string    Helper output
     */
    public function renderHelper($identifier, $config = array())
    {
        //Get the function to call based on the $identifier
        $parts = explode('.', $identifier);
        $function = array_pop($parts);

        $helper = $this->getHelper(implode('.', $parts), $config);

        //Call the helper function
        if (!is_callable(array($helper, $function))) {
            throw new KTemplateHelperException(get_class($helper) . '::' . $function . ' not supported.');
        }

        return $helper->$function($config);
    }

    /**
     * Searches for the file
     *
     * @param    string    The file path to look for.
     * @return    mixed    The full path and file name for the target file, or FALSE if the file is not found
     */
    public function findFile($file)
    {
        $result = false;
        $path = dirname($file);

        // is the path based on a stream?
        if (strpos($path, '://') === false)
        {
            // not a stream, so do a realpath() to avoid directory
            // traversal attempts on the local file system.
            $path = realpath($path); // needed for substr() later
            $file = realpath($file);
        }

        // The substr() check added to make sure that the realpath()
        // results in a directory registered so that non-registered directores
        // are not accessible via directory traversal attempts.
        if (file_exists($file) && substr($file, 0, strlen($path)) == $path) {
            $result = $file;
        }

        // could not find the file in the set of paths
        return $result;
    }

    /**
     * Process the template using a simple sandbox
     *
     * This function passes the template through the read filter chain before letting the PHP parser
     * executed it. The result is buffered.
     *
     * @param  boolean     If TRUE apply write filters. Default FALSE.
     * @return KTemplateAbstract
     */
    private function __sandbox()
    {
        //Push the template onto the stack
        $this->getStack()->push(clone $this);

        //Extract the data in local scope
        extract($this->_data, EXTR_SKIP);

        // Capturing output into a buffer
        ob_start();
        include 'tmpl://'.$this->getFile()->filepath;
        $this->_contents = ob_get_clean();

        //Remove the template from the template stack
        $this->getStack()->pop();

        return $this;
    }

    /**
     * Renders the template and returns the result
     *
     * @return     string
     */
    public function __toString()
    {
        try {
            $result = $this->_contents;
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }
}