<?php

class ComApplicationControllerBehaviorRoutable extends KControllerBehaviorAbstract
{
	protected $_pages;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_pages = $this->getPages();
	}
	
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'priority' => KCommand::PRIORITY_HIGHEST
		));

		parent::_initialize($config);
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

	    	// Set the global instance of the pages
    		KService::set('com://site/application.pages', $this->_pages);
    		KService::setAlias('pages', 'com://site/application.pages');

    		// Set the global instance of the current page
    		KService::set('com://site/application.pages.current', $page);
    		KService::setAlias('pages.current', 'com://site/application.pages.current');
		}

		return $this->_pages;
	}
	
	protected function _beforeDispatch(KCommandContext $context)
    {
    	$page = $this->getService('pages.current');

    	if (!$page) {
    		// @TODO: redirect to 404 page not found
    	}

    	// Get the component's router. Expects $page->type to be an identifier.
    	$component = clone $this->getIdentifier($page->type);
    	$component->name = 'router';

    	// Get the parameters based on the route
    	$parameters = $this->getService($component)->parse($this->getService('pages.current')->uri);

    	// Determine the type of content the page is trying to access and call it.
		$component->name = 'dispatcher';

		$context->caller->setRequest($page->parameters);
		$context->caller->setComponent($component);
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
}