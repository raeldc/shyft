<?php

/*
 * The Application Router that knows the context of the page
 *		This prepends application related uri to the page's uri.
 */
final class ComApplicationRouter extends SRouterDefault
{
	protected $_context;
	protected $_sefurl;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		// Get pages from the database using a model
		$this->getPages($config->pages);

		// Get the confiugration if SEF URL is activated
		$this->_sefurl = $config->sefurl;

		// Get the context based on the URI
		$this->getContext();
	}
	
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'routes' => array(
				'[<lang>/]admin/manage/<uri>[.<format>]'    => 'mode=admin&com=pages&format=html&lang=default',
				'[<lang>/]admin[/<com>][/<uri>][.<format>]' => 'com=dashboard&mode=admin&format=html&lang=default',
				'[<lang>/][<page>/]<uri>[.<format>]'        => 'mode=site&page=default&format=html&lang=default',
			),
			'regex' => array(
				'lang'	 => '^[a-z]{2,2}|^[a-z]{2,2}-[a-z]{2,2}',
				'uri'    => '[a-zA-Z0-9\-+.:_/]*',
				'format' => '[a-z]+$',
				// @TODO: must be populated by all installed components
				'com'	 => 'widgets|pages|staticpage|content|dashboard',
				// @TODO: must be populated by all installed components
				'page'   => 'home'
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
			if ($this->_sefurl) {
				$this->_context = parent::parse($this->getUri());	
			}
			else
			{
				$this->_context = new KConfig(array(
					'mode'   => KRequest::get('mode', 'cmd', 'site'),
					'format' => KRequest::get('format', 'cmd', 'html'),
					'lang'   => KRequest::get('lang', 'cmd', 'default'),
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

	public function getPages()
	{
		// @TODO: Cache the pages. Which one is faster, db querying? or using Rowset::find()?

		if(!$this->_pages) 
		{
			$uri = $this->getUri();

	    	// Get the page alias from the URI
	    	$segments = explode('/', $uri, 2);

			$this->_pages = $this->getService('com://site/pages.model.pages')
    			->enabled(true)
    			->getList();

    		// Find the page that has the page alias or just use the default page
	    	if(($page = $this->_pages->find(array('permalink' => $segments[0]))->current()) === false) {
	    		$page = $this->_pages->find(array('default' => true))->current();
	    	}
			// If page is the default, use the whole URI, if not use the 2nd segment
    		$page->uri = ($page->default) ? $uri : $segments[1];
		}

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
				throw new KDispatcherException('Unable to get the URI using PATH_INFO, REQUEST_URI, PHP_SELF or REDIRECT_URL');
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