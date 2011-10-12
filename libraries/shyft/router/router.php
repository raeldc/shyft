<?php

/* 
 * Routers build pretty urls from a URL Query, they can also parse pretty URLs into a query array 
 *		First, create rules on how a URL is parsed or built.
*/
class SRouter extends KObject
{
	// Define the pattern of a <parameter>
	const REGEX_KEY     = '<([a-zA-Z0-9_]++)>';

	// Legal Characters for a <parameter>
	const REGEX_PARAMETER = '[^/.,;?\n]++';

	// What must be escaped in the route regex
	const REGEX_ESCAPE  = '[.\\+*?(^\\)${}=!|]';

	/**
     * List of route rules
     *
     * @var array
     */
	protected $_routes;

	/**
     * List of regex rules for a paramter
     *
     * @var array
     */
	protected $_regex;

	/**
     * The alias of a component
     *
     * @var string
     */
	protected $_alias;
	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		if (empty($config->alias)) {
			throw new SRouterException('Alias was not set for the router');
		}

		$this->_routes = new ArrayObject();
		$this->_regex = $config->regex->toArray();

		$this->addRoutes($config->routes, $this->_regex);
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'alias'  => $this->getIdentifier()->package,
			'routes' => array(),
			'regex'  => array()
		));
	
		parent::_initialize($config);
	}

	public function addRoutes($routes, $regex = array())
	{ 
        $routes = (array) KConfig::unbox($routes);
         
        foreach($routes as $uri => $query)
        {
        	$object = new KConfig();
        	$object->rule = $uri;
        	$object->uri = $this->compile($uri, $regex);
        	$object->query = $query;

            //Add the routes
            $this->_routes[$uri] = $object;
        }

        return $this;
    }

	/**
	 * Returns a string of pretty URL based on query that matches a route
	 *
	 */
	public function build($httpquery)
	{
		parse_str($httpquery, $query);

		$route = $this->getMatch($query);

		$uri = $route->rule;
		parse_str($route->query, $defaults);

		if (strpos($uri, '<') === FALSE AND strpos($uri, '[') === FALSE)
		{
			// This is a static route, no need to replace anything
			return $uri;
		}

		while (preg_match('#\[[^\[\]]++\]#', $uri, $match))
		{
			// Search for the matched value
			$search = $match[0];

			// Remove the square brackets from the match as the replace
			$replace = substr($match[0], 1, -1);

			while(preg_match('#'.SRouter::REGEX_KEY.'#', $replace, $match))
			{
				list($key, $param) = $match;

				// If the optional parameter is equal to defaults, don't replace it.
				if (isset($query[$param]) && $query[$param] != $defaults[$param] && $query[$param] != '#'.$defaults[$param])
				{
					// Replace the key with the parameter value
					$replace = str_replace($key, $query[$param], $replace);

					// Remove the param because it's already injected into the URI
					unset($query[$param]);
				}
				else
				{
					// This group has missing parameters
					$replace = '';
					break;
				}
			}

			// Replace the group in the URI
			$uri = str_replace($search, $replace, $uri);
		}

		while(preg_match('#'.SRouter::REGEX_KEY.'#', $uri, $match))
		{
			list($key, $param) = $match;

			if(!isset($query[$param])) {
				throw new SRouterException('Required route parameter not passed');
			}

			$uri = str_replace($key, $query[$param], $uri);

			// Remove the param because it's already injected into the URI
			unset($query[$param]);
		}

		// Remove unnecessary slashes
		$uri = preg_replace('#//+#', '/', rtrim($uri, '/'));

		// Remove params from query if it's already in the defaults
		foreach ($query as $key => $value) 
		{
			if (array_key_exists($key, $defaults)) {
				unset($query[$key]);
			}
		}

		// Add the remaining parameters as regular url queries
		if (count($query)) {
			$uri .= '?'. http_build_query($query);
		}

		return $uri;
	}

	/**
	 * Returns an array of variables parsed from the Route
	 *
	 */
	public function parse($uri)
	{
		foreach ($this->_routes as $route) {
			if (preg_match($route->uri, $uri, $matches)) break;
		}

		$params = array();
		foreach($matches as $key => $value)
		{
			if(is_int($key))
			{
				// Skip all unnamed keys
				continue;
			}

			// Set the value for all matched keys
			$params[$key] = $value;
		}

		parse_str($route->query, $query);

		// Populate params with default values based on the query
		foreach($query as $key => $value)
		{
			// Set default values for any key that was not matched
			if(!isset($params[$key]) or $params[$key] === '')
			{
				// In finding the route rule to use, # indicates that a different value gets higher points
				if (strpos($value, '#') !== false) {
					$params[$key] = substr($value, 1);
				}
				else $params[$key] = $value;
			}
		}

		return $params;
	}

	public static function compile($uri, array $regex = NULL)
	{
		if ( ! is_string($uri))
			return;

		// The URI should be considered literal except for keys and optional parts
		// Escape everything preg_quote would escape except for : ( ) < >
		$expression = preg_replace('#'.SRouter::REGEX_ESCAPE.'#', '\\\\$0', $uri);

		if (strpos($expression, '[') !== false)
		{
			// Make optional parts of the URI non-capturing and optional
			$expression = str_replace(array('[', ']'), array('(?:', ')?'), $expression);
		}

		// Insert default regex for keys
		$expression = str_replace(array('<', '>'), array('(?P<', '>'.SRouter::REGEX_PARAMETER.')'), $expression);

		if ($regex)
		{
			$search = $replace = array();
			foreach ($regex as $key => $value)
			{
				$value = is_array($value) ? implode('|', $value) : $value;
				$search[]  = "<$key>".SRouter::REGEX_PARAMETER;
				$replace[] = "<$key>$value";
			}

			// Replace the default regex with the user-specified regex
			$expression = str_replace($search, $replace, $expression);
		}

		return '#^'.$expression.'$#uD';
	}

	/**
	 * Find the rule that best matches the query string
	 *
	 */
	public function getMatch($httpquery)
	{
		// @TODO: Cache the results

		if (!is_array($httpquery)) {
			parse_str($httpquery, $query);
		}
		else $query = $httpquery;

		// We will match the query based on how similar they are
		$best_match = 'default';
		$similarities = array('default' => 0);

		foreach ($this->_routes as $route) 
		{
			$similarities[$route->rule] = $this->getSimilarity($route->query, $query);
			$best_match = ($similarities[$route->rule] > $similarities[$best_match]) ? $route->rule : $best_match;
		}

		return $this->_routes[$best_match];
	}

	/**
	 * Calculate similarity based on how much keys and values match.
	 * 		Add one point for each similar key or value
	 *
	 */
	public function getSimilarity($base_string, $match_string)
	{
		if (!is_array($match_string)) {
			parse_str($match_string, $match);
		}
		else $match = $match_string;

		parse_str($base_string, $base);

		$points = 0;

		foreach ($base as $key => $value) 
		{
			if (array_key_exists($key, $match))
			{
				// Add 1 point if the key exists
				$points++;

				// Add 1 point if the parameter is equal to the current value
				if ($match[$key] == $value) {
					$points++;
				}
				elseif(substr($value, 0, 1) === '#') 
				{
					// Add a point if the required parameter is different from default
					if(substr($value, 1) != $match[$key])
						 $points++;
				}
			}
		}

		return $points;
	}
}