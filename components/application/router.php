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
	}
	
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				// Get route for the component                     => Use it when com is set, and mode is admin.
				'[<lang>/]admin[/<com>[/<uri>][.<format>]]'        => 'mode=#admin&com=!&lang=en&format=html&com=pages',

				// Get route for page management                   => Use it when page is set and mode is admin
				'[<lang>/]admin/pages[/<page>[/<uri>][.<format>]]' => 'mode=#admin&page=!&lang=en&format=html',

				// Get route for a URI with page                   => Use this route if page or uri is set
				'[<lang>/][<page>[/<uri>][.<format>]]'             => 'mode=#site&lang=en&format=html&page=!&uri=!',

				// Get route if lang is set                        => Use this if lang is set
				'[<lang>[/<uri>[.<format>]]]'                      => 'mode=#site&lang=!en&format=html&page=#&uri=#',

				// Get route if no page is set but there's a URI   => Use this if no uri or page is set
				'[<lang>/][<uri>[.<format>]]'                      => 'mode=#site&lang=en&format=html&page=#&uri=#',
			),
			'regex' => array(
				'lang'	 => '^[a-z]{2,2}|^[a-z]{2,2}-[a-z]{2,2}',
				'uri'    => '[a-zA-Z0-9\-+.:_/]+',
				'format' => '[a-z]+$',
				// @TODO: must be populated by all installed components.
				'com'	 => array('dashboard','widgets'),
				// @TODO: must be populated by all enabled pages
				'page'   => array('home', 'contents'),
			),
			'defaults' => array(
				'mode'   => KRequest::get('get.mode', 'cmd', 'site'),
				'lang'   => KRequest::get('get.lang', 'cmd', 'en'),
				'format' => KRequest::get('get.format', 'cmd', 'html'),
				'page'   => KRequest::get('get.page', 'cmd', ''),
				'uri'    => KRequest::get('get.page', 'cmd', ''),
				'com'    => KRequest::get('get.com', 'cmd', 'dashboard'),
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

			if(!$this->_sefurl){
				$this->_context->application = new KConfig($this->_defaults);
			}else{ 
				$this->_context->application = new KConfig(parent::parse($this->getUri()));
			}

			$this->_context->component = new KConfig();
			$component = '';

			// Get the page, it will use the default page if page is not found
			$this->_context->page = $this->getPage($this->_context->application->page);

			// If the mode is site, get the component request from the current page
			if($this->_context->application->mode == 'site') 
			{
				// In the frontend, there is no way to access a component without going through a page.
				$component = $this->_context->component->com = $this->_context->page->component;

				// If the page has default parameters, merge them to the component's context
				$this->_context->component->append($this->_context->page->parameters);
			}
			// If we're not on 'site' and the page not set, but there is a component in the parameters, use that component.
			elseif(empty($this->_context->application->page) && !empty($this->_context->application->com))
			{
				$component = $this->_context->component->com = $this->_context->application->com;
			}
			// Use the default component from the default page
			else $component = $this->_context->application->com = $this->_context->page->component;

			// If there is a sub-uri, it will be used as the component context
			// 		This can only happen in the admin mode
			if(!empty($this->_context->application->uri) && $this->_sefurl) 
			{
				$this->_context->uri = $this->_context->application->uri;

				// Get the component's router and parse the rest of the URI
				$this->_context->component->append(
					$this->getRouter($component)
						->parse($this->_context->application->uri)
				);
			}
			// Else we just get the request values from $_GET
			else $this->_context->component->append(KRequest::get('get', 'string'));

			// The application context should stay untouched
			$application = clone $this->_context->application;

			// Merge the request to the request from the application URI
			$this->_context->component = $application->append($this->_context->component);

			// We don't want the URI to be accessible after this
			unset($this->_context->component->uri);
			unset($this->_context->application->uri);
		}

		return $this->_context;
	}

	public function build($httpquery)
	{
		// $httpquery is expected to come from the component.
		parse_str($httpquery, $query);	

		$application = $this->_context->application->toArray();

		// Merge the query's values into application's context
		foreach($query as $key => $value) 
		{
			// Always merge if not using SEF
			if(!$this->_sefurl)
			{
				$application[$key] = $value;	
			}
			elseif(isset($application[$key])) 
			{
				// Merge only if the application has the same parameter
				$application[$key] = $value;

				// Once it is merged, don't include it in the query. Prevents /resource/id?resource=resource&id=id
				unset($query[$key]);
			}
		}

		if($this->_sefurl) 
		{
			$component = null;

			// $query['page'] is usually expected in the pages management mode.
			// If the page is set in the application or in the component's query
			if(isset($query['page']) | isset($application['page'])) {
				// Don't include the application's component in building route
				unset($application['com']);
			}
			else $component = $application['com'];

			// For the frontend, the component can set the page. So if it does, set it in the application context
			if(isset($query['page']) && ($application['mode'] == 'site' || isset($query['base']))){
				$application['page'] = $query['page'];
			}

			if(is_null($component)){
				$component = $this->getPage($application['page'])->component;
			}

			// If base is true, use only the application context to build the route.
			if(isset($query['base'])) {
				return KRequest::base().'/'.parent::build(http_build_query($application));
			}

			// If we're not on base, will use the component's router to build the URI
			$application['uri'] = $this->getRouter($component)->build(http_build_query($query));

			$getvars = '';
			if(strpos($application['uri'], '?') !== false) {
				list($application['uri'], $getvars) = explode('?', $application['uri'], 2);
				$getvars = '?'.$getvars;
			}

			// Build the complete URI along with the component's URI.
			$result = KRequest::base().'/'.parent::build(http_build_query($application)).$getvars;

			return $result;
		}

		// We don't want base to be included in the URI
		unset($application['base']);

		return KRequest::base().'/index.php?'.http_build_query($application);
	}

	public function getRouter($component)
	{
		if(!isset($this->_routers[$component])) 
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

		if(!empty($permalink)) 
		{
			if(!in_array($permalink, array('default','admin','manage')))
			{
				$page = $pages->find(array('permalink' => $permalink))->current();
			}
		}

		if(is_null($page)) {
			$page = $pages->find(array('default' => true))->current();	
		}

		if(is_null($page)) {
			throw new SRouterException('No Default page found');
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

			if(!($model instanceof KModelAbstract)) {
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
		if(!empty($_SERVER['PATH_INFO']))
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

				if($request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
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