<?php

/*
 * The Application Router that knows the context of the page
 *		This prepends application related uri to the page's uri.
 */
final class ComApplicationRouter extends SRouterDefault
{
	protected $_context;
	protected $_sefurl;
	protected $_pages;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		// Get pages from the database using a model
		$this->_pages = $config->pages;

		// Get the confiugration if SEF URL is activated
		$this->_sefurl = $config->sefurl;

		// Get the context based on the URI
		$this->getContext();
	}
	
	protected function _initialize(KConfig $config)
	{
		$default = 'mode=site&page=default&format=html&lang=default';

		$config->append(array(
			'routes' => array(
				'[<lang>/]admin/manage/<uri>[.<format>]'    => 'mode=admin&com=pages&format=html&lang=default',
				'[<lang>/]admin[/<com>][/<uri>][.<format>]' => 'com=dashboard&mode=admin&format=html&lang=default',
				'<lang>[.<format>]|<lang>/<page>[.<format>]|<page>[.<format>]|[<lang>/][<page>/]<uri>[.<format>]' => $default, 
			),
			'regex' => array(
				'lang'	 => '^[a-z]{2,2}|^[a-z]{2,2}-[a-z]{2,2}',
				'uri'    => '[a-zA-Z0-9\-+.:_/]+',
				'format' => '[a-z]+$',
				// @TODO: must be populated by all installed components.
				'com'	 => array('dashboard'),
				// @TODO: must be populated by all enabled pages
				'page'   => array('default', 'home'),
			),
			// Inject the pages that the router will use
			'pages' => 'com://site/pages.model.pages',
			'sefurl' => true,
		));

		parent::_initialize($config);
	}

	public function getContext()
	{
		if(!($this->_context instanceof KConfig)) 
		{
			if ($this->_sefurl) 
			{
				$this->_context = new KConfig(parent::parse($this->getUri()));

				// If the mode is site, automatically set the page to current page
				if ($this->_context->mode == 'site') 
				{
					$this->_context->page = $this->getPage();
					$this->_context->com = $this->_context->page->component;
					$this->_context->request = array();

					if (!empty($this->_context->uri)) 
					{
						// Get the component's router and parse the rest of the URI
						$this->_context->request = $this->getRouter($this->_context->page->component)
							->parse($this->_context->uri);	
					}

					if (empty($this->_context->request)) 
					{
						// If the uri is empty, get request from page's default parameters
						$this->_context->request = $this->_context->page->parameters;
					}
				}
			}
			else
			{
				$this->_context = new KConfig(array(
					'mode'   => KRequest::get('mode', 'cmd', 'site'),
					'lang'   => KRequest::get('lang', 'cmd', 'default'),
					'format' => KRequest::get('format', 'cmd', 'html'),
					'page'   => KRequest::get('page', 'cmd', 'default'),
					'com'    => KRequest::get('com', 'cmd', 'dashboard'),
				));
			}
		}

		return $this->_context;
	}

	public function build($httpquery, $router)
	{
		// @TODO: This method should decide if it will use SEF URLs or not.

		$prefix = parent::build($this->_context);
		$uri    = $router->build($httpquery);

		return KRequest::base().'/'.$prefix.'/'.$uri;
	}

	public function getRouter($component)
	{
		$identifier = clone $this->getIdentifier();
		$identifier->package = $component;
		$identifier->path    = array();
		$identifier->name    = 'router';

		return $this->getService($identifier);
	}

	public function getPage($permalink = null)
	{
		$page = null;
		$pages = $this->getPages();

		if (!is_null($permalink)) 
		{
			if (!in_array($permalink, array('default', 'admin','manage')))
			{
				$page = $pages->find(array('permalink' => $permalink))->current();
			}
		}

		if (is_null($page)) {
			$page = $pages->find(array('default' => true))->current();	
		}

		if (is_null($page)) {
			// @TODO: Redirect to 404 Not Found
		}

		return $page;
	}

	public function getPages()
	{
		// @TODO: Cache the pages. Which one is faster, db querying? or using Rowset::find()?
		if(!$this->_pages instanceof KDatabaseRowsetAbstract)
		{	   
		    //Make sure we have an identifier
		    if(!($this->_pages instanceof KServiceIdentifier)) {
		        $this->setPages($this->_pages);
			}

			$model = $this->getService($this->_pages);

			if (!($model instanceof KModelAbstract)) {
				throw new SRouterException('Pages is not an instance of KModelAbstract');
			}

			$this->_pages = $model->enabled(true)->getList();
		}
		
		return $this->_pages;
	}

	public function setPages($pages)
	{
		if(!($pages instanceof KDatabaseRowsetAbstract))
		{
			if(is_string($pages) && strpos($pages, '.') === false ) 
		    {
			    $identifier          = clone $this->getIdentifier();
			    $identifier->package = 'pages';
			    $identifier->path    = array('model');
			    $identifier->name    = 'pages';
			}
			else $identifier = $this->getIdentifier($pages);

			$pages = $identifier;
		}

		$this->_pages = $pages;

		return $this->_pages;
	}
	
	/**
	 * Get the URI of the request using PATH_INFO,
	 * REQUEST_URI, PHP_SELF or REDIRECT_URL.
	 *
	 * @return  string  URI of the request
	 * @throws  KDispatcherException
	 */
	public static function getUri()
	{
		if (!empty($_SERVER['PATH_INFO']))
		{
			// PATH_INFO does not contain the docroot or index
			$uri = $_SERVER['PATH_INFO'];
		}
		else
		{
			// REQUEST_URI and PHP_SELF include the docroot and index

			if(isset($_SERVER['REQUEST_URI']))
			{
				$uri = $_SERVER['REQUEST_URI'];

				if ($request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
				{
					// Valid URL path found, set it.
					$uri = $request_uri;
				}

				// Decode the request URI
				$uri = rawurldecode($uri);
			}
			elseif(isset($_SERVER['PHP_SELF'])) {
				$uri = $_SERVER['PHP_SELF'];
			}
			elseif(isset($_SERVER['REDIRECT_URL'])) {
				$uri = $_SERVER['REDIRECT_URL'];
			}
			else{ 
				throw new SRouterException('Unable to get the URI using PATH_INFO, REQUEST_URI, PHP_SELF or REDIRECT_URL');
			}

			// Get the path from the base URL, including the index file
			$base_url = parse_url(KRequest::url(), PHP_URL_PATH);

			if(strpos($uri, $base_url) === 0)
			{
				// Remove the base URL from the URI
				$uri = (string) substr($uri, strlen($base_url));
			}

			// Remove the index file from the URI
			$uri = (string) substr($uri, strlen('index.php'));
		}

		return trim($uri, '/');
	}

	public function __get($name)
	{
		return $this->_context->$name;
	}
}