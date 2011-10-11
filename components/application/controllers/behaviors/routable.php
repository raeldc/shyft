<?php

class ComApplicationControllerBehaviorRoutable extends KControllerBehaviorAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'priority' => KCommand::PRIORITY_HIGHEST,
		));

		parent::_initialize($config);
	}
	
	protected function _beforeDispatch(KCommandContext $context)
    {
    	$uri = $this->getUri();

    	// Get the page alias from the URI
    	$segments = explode('/', $uri, 2);

    	// @TODO: Cache the pages
    	// Get the page model to get the current item
    	$pages = $this->getService('com://site/pages.model.page')
    		->getList();

    	// Find the page that has the page alias or just use the default page
    	$page = ($page = $pages->find(array('permalink' => $segments[0]))->current()) === false ? $pages->find(array('default' => true))->current() : $page;

    	if (!$page) {
    		// @TODO: redirect to 404 page not found
    	}

    	// If page is the default, use the whole URI, if not use the 2nd segment
    	$uri = ($page->default) ? $uri : $segments[1];

    	// Get the component's router
    	$router = clone $this->getIdentifier($page->type);
    	$router->name = 'router';

    	// Get the parameters based on the route
    	$parameters = $this->getService($router)->parse($uri);

    	// Determine the type of content the page is trying to access and call it.
    	$component = clone $this->getIdentifier($page->type);
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
				/**
				 * We use REQUEST_URI as the fallback value. The reason
				 * for this is we might have a malformed URL such as:
				 *
				 *  http://localhost/http://example.com/judge.php
				 *
				 * which parse_url can't handle. So rather than leave empty
				 * handed, we'll use this.
				 */
				$uri = $_SERVER['REQUEST_URI'];

				if ($request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
				{
					// Valid URL path found, set it.
					$uri = $request_uri;
				}

				// Decode the request URI
				$uri = rawurldecode($uri);
			}
			elseif(isset($_SERVER['PHP_SELF']))
			{
				$uri = $_SERVER['PHP_SELF'];
			}
			elseif(isset($_SERVER['REDIRECT_URL']))
			{
				$uri = $_SERVER['REDIRECT_URL'];
			}
			else
			{
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