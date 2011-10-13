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
	protected $_routers = array();

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
		$config->append(array(
			'routes' => array(
				'[<lang>/]admin/pages'                           => 'mode=admin&com=pages&format=html&lang=default',
				'[<lang>/]admin/pages/<page>[/<uri>][.<format>]' => 'mode=admin&format=html&lang=default&page=#&uri=#',
				'[<lang>/]admin[/<com>][/<uri>][.<format>]'      => 'com=dashboard&mode=admin&format=html&lang=default',
				'<lang>[.<format>]'                              => 'mode=site&page=default&format=html&lang=#default',
				'<page>[.<format>]'                              => 'mode=site&page=#default&format=html&lang=default',
				'<lang>/<page>[.<format>]'                       => 'mode=site&page=#default&format=html&lang=#default',
				'[<lang>/][<page>/][<uri>][.<format>]'           => 'mode=site&page=#default&format=#html&lang=#default&uri=#',
			),
			'regex' => array(
				'lang'	 => '^[a-z]{2,2}|^[a-z]{2,2}-[a-z]{2,2}',
				'uri'    => '[a-zA-Z0-9\-+.:_/]+',
				'format' => '[a-z]+$',
				// @TODO: must be populated by all installed components.
				'com'	 => array('dashboard'),
				// @TODO: must be populated by all enabled pages
				'page'   => array('home', 'pages', 'contents', 'widgets'),
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
			$this->_context = new KConfig();

			if ($this->_sefurl) {
				$this->_context->application = new KConfig(parent::parse($this->getUri()));
			}
			else
			{
				$this->_context->application = new KConfig(array(
					'mode'   => KRequest::get('get.mode', 'cmd', 'site'),
					'lang'   => KRequest::get('get.lang', 'cmd', 'default'),
					'format' => KRequest::get('get.format', 'cmd', 'html'),
					'page'   => KRequest::get('get.page', 'cmd', 'default'),
					'com'    => KRequest::get('get.com', 'cmd', 'dashboard'),
				));
			}

			$this->_context->component = new KConfig();

			$this->_context->page = $this->getPage($this->_context->application->page);

			// If the mode is site, get the component request from the current page
			if ($this->_context->application->mode == 'site') 
			{
				$this->_context->component->com = $this->_context->page->component;

				if (!$this->_context->component->count()) 
				{
					// If the uri is empty, get request from page's default parameters
					$this->_context->component->append($this->_context->page->parameters);
				}
			}
			elseif(empty($this->_context->application->page) && !empty($this->_context->application->com))
			{
				$this->_context->component->com = $this->_context->application->com;
			}
			else $this->_context->application->com = $this->_context->page->component;

			if (!empty($this->_context->application->uri) && $this->_sefurl) 
			{
				// Get the component's router and parse the rest of the URI
				$this->_context->component->append(
					$this->getRouter($this->_context->application->com)
						->parse($this->_context->application->uri)
				);
			}
			// Else we just get the request values from $_GET
			else $this->_context->component->append(KRequest::get('get', 'string'));

			$application = clone $this->_context->application;

			// Merge the request to the request from the application URI
			$this->_context->component = $application->append($this->_context->component);

			// We don't want the URI to be accessible in the component level
			unset($this->_context->component->uri);
		}

		return $this->_context;
	}

	public function build($httpquery)
	{
		// $httpquery is expected to come from the component.
		if (!is_array($httpquery)) {
			parse_str($httpquery, $query);	
		}
		else $query = $httpquery;

		$application = $this->_context->application->toArray();

		// Merge the query's values into application's context
		foreach ($query as $key => $value) 
		{
			if (isset($application[$key])) 
			{
				$application[$key] = $value;
				unset($query[$key]);
			}
			elseif (!$this->_sefurl) {
				$application[$key] = $value;
			}
		}

		if ($this->_sefurl) 
		{
			$component = $application['com'];

			// @TODO: Refactor this. There must be a way to make this code prettier.
			if (isset($query['page']) | isset($application['page'])) {
				unset($application['com']);
			}

			if (isset($query['page']) && ($application['mode'] == 'site' || isset($query['base']))) {
				$application['page'] = $query['page'];
			}

			if (isset($query['base'])) {
				return KRequest::base().'/'.parent::build($application);
			}

			$application['uri'] = $this->getRouter($component)->build($query);

			$result = KRequest::base().'/'.parent::build($application);

			return $result;
		}

		return KRequest::base().'/index.php?'.http_build_query($application);
	}

	public function getRouter($component)
	{
		if (!isset($this->_routers[$component])) 
		{
			$identifier          = clone $this->getIdentifier();
			$identifier->package = $component;
			$identifier->path    = array();
			$identifier->name    = 'router';

			$this->_routers[$component] = $this->getService($identifier);
		}
		
		return $this->_routers[$component];
	}

	public function getPage($permalink = null)
	{
		$page = null;
		$pages = $this->getPages();

		if (!is_null($permalink)) 
		{
			if (!in_array($permalink, array('default', 'admin','manage')) && is_null($page))
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