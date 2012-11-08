<?php

/*
 * The Application Router that knows the request of the page
 *		This prepends application related uri to the page's uri.
 */
final class ComApplicationRouter extends SRouterDefault
{
	protected $_request;
	protected $_pages;
	protected $_page_list;
	protected $_sefurl;
	protected $_routers = array();

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		// Get the confiugration if SEF URL is activated
		$this->_sefurl = $config->sefurl;
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				// Get route for the component                     => Use it when com is set, and mode is admin.
				'[<lang>/]admin[/<com>[/<uri>][.<format>]]'        => 'mode=#admin&com=!dashboard',

				// Get route for page management                   => Use it when page is set and mode is admin
				'[<lang>/]admin/pages[/<page>][/<uri>][.<format>]' => 'mode=#admin&page=!&com=pages',

				// Lang is optinally set and page is set           => Use this route if page or uri is set
				'[<lang>][/][<page>[/<uri>][.<format>]]'           => 'mode=#site&page=!&uri=!',

				// This is the catch all rule 					   => Use this if page and uri is blank
				'[<lang>][/][<uri>[.<format>]]'                    => 'mode=#site&page=#&uri=#',
			),
			'regex' => array(
				'lang'	 => '^[a-z]{2,2}|^[a-z]{2,2}-[a-z]{2,2}',
				'uri'    => '[a-zA-Z0-9\-+.:_/]+',
				'format' => '[a-z]+$',
				// @TODO: must be populated by all installed components.
				'com'	 => array('dashboard','widgets'),
				// @TODO: must be populated by all enabled pages
				'page'   => $this->getPageList(),
			),
			'defaults' => array(
				'mode'   => 'site',
				'lang'   => 'en',
				'format' => 'html',
				'page'   => '',
				'uri'    => '',
				'com'    => 'dashboard',
			),
			'sefurl' => true,
		));

		parent::_initialize($config);
	}

	public function getRequest()
	{
		if(!($this->_request instanceof KConfig))
		{
			if(!$this->_sefurl){
				$application = new KConfig(array_merge($this->_defaults, KRequest::get('get', 'string')));
			}else{
				$application = new KConfig($this->parse($this->getUri()));
			}

			// Start with an empty request values for the component
			$component = array();

			// If site mode, or if the component being accessed is pages(happens only in admin mode)
			if($application->mode == 'site' || ($application->com == 'pages' && !empty($application->page)))
			{
				// Get the page, it will use the default page if page is not found
				$page              = $this->getPage($application->page);
				$application->com  = $page->type->component;
				$application->page = $page->id;
				$component         = $page->parameters;
			}

			// If sefurl is turned on
			if($this->_sefurl && !empty($application->uri))
			{
				$component = $this->getRouter($application->com)
						->parse($application->uri);
			}
			// Else we just get the request values from $_GET
			elseif(!$this->_sefurl)
				$component = array_merge($component, KRequest::get('get', 'string'));

			// Clear all empty application parameters so that values from component can be appended to it
			foreach ($application as $key => $value) {
				if (empty($application[$key])) {
					unset($application[$key]);
				}
			}

			// Create the request
			$this->_request = new KConfig(array(
				// The application request should stay untouched, so clone it
				'application' => clone $application,
				'component'   => $application->append($component)
			));
		}

		// We return the component request only coz that's the only thing relevant outside
		return $this->_request->component;
	}

	public function build($httpquery)
	{
		// $httpquery is expected to come from the component.
		parse_str($httpquery, $query);

		$application = $this->_request->application->toArray();

		if (!$this->_sefurl)
		{
			// Merge all values from query to application
			$application = array_merge($application, $query);

			// We don't want these to be included in the URI
			unset($application['base']);
			unset($application['uri']);

			// If mode is site, remove the component value
			if ($application['mode'] == 'site') {
				unset($application['com']);
			}

			// Remove values that are equal to defaults
			foreach ($application as $key => $value)
			{
				if (isset($this->_defaults[$key]) && $this->_defaults[$key] == $value) {
					unset($application[$key]);
				}
			}

			return KRequest::base().'/index.php?'.http_build_query($application);
		}

		// Merge the query's values into application's request
		foreach($query as $key => $value)
		{
			if(isset($application[$key]))
			{
				// Merge only if the application has the same parameter
				$application[$key] = $value;

				// Once it is merged, don't include it in the query. Prevents /resource/id?resource=resource&id=id
				unset($query[$key]);
			}
		}

		$component = null;

		// $query['page'] is usually expected in the pages management mode.
		// If the page is set in the application or in the component's query
		if((isset($query['page']) && !empty($query['page'])) | (isset($application['page']) && !empty($application['page']))) {
			// Don't include the application's component in building route
			unset($application['com']);
		}
		else $component = $application['com'];

		// For the frontend, the component can set the page. So if it does, set it in the application request
		if(isset($query['page']) && ($application['mode'] == 'site' || isset($query['base']))){
			$application['page'] = $query['page'];
		}

		if(is_null($component)){
			$component = $this->getPage($application['page'])->type->component;
		}

		// If base is true, use only the application request to build the route.
		if(isset($query['base']))
		{
			unset($application['uri']);
			$uri = parent::build(http_build_query($application));
			return (!empty($uri)) ? KRequest::base().'/'.$uri : KRequest::base();
		}

		// If we're not on base, will use the component's router to build the URI
		$application['uri'] = $this->getRouter($component)->build(http_build_query($query));

		$getvars = '';

		// Get the extra get vars from the component's router which will be appended on the final URI.
		if(strpos($application['uri'], '?') !== false)
		{
			list($application['uri'], $getvars) = explode('?', $application['uri'], 2);
			$getvars = '?'.$getvars;
		}

		// Build and return the complete URI along with the component's URI.
		$uri = parent::build(http_build_query($application)).$getvars;
		return (!empty($uri)) ? KRequest::base().'/'.$uri : KRequest::base();
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

	public function getPage($id = null)
	{
		$page = null;
		$pages = $this->getPages();

		if(!empty($id))
		{
			if(!in_array($id, array('default','admin','manage')))
			{
				$page = $pages->find($id);
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
			$this->_pages = clone $this->getService('com://site/pages.model.pages')
				->all(true)
				->getList();
		}

		return $this->_pages;
	}

	public function getPageList()
	{
		// @TODO: cache the result
		if (is_null($this->_page_list))
		{
			$pages = $this->getPages();
			$this->_page_list = '';

			foreach ($pages as $page)
			{
				if (!empty($this->_page_list)) {
					$this->_page_list .= '|';
				}

				$this->_page_list .= $page->id;
			}
		}

		return $this->_page_list;
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
		return $this->_request->$name;
	}
}