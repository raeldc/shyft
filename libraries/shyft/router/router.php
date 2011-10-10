<?php

/* 
 * Routers build pretty urls from a URL Query, they can also parse pretty URLs into a query array 
 *		First, create rules on how a URL is parsed or built.
*/
abstract class SRouter extends KObject
{
	// Define the pattern of a :parameter
	const REGEX_KEY     = '<([a-zA-Z0-9_]++)>';

	// Legal Characters for a :parameter
	const REGEX_PARAMETER = '[^/.,;-?\n]++';

	// What must be escaped in the route regex
	const REGEX_ESCAPE  = '[.\\+*?[^\\]${}=!|]';

	/**
     * List of route rules
     *
     * @var array
     */
	protected $_routes;

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

		$this->addRoutes($config->routes);
	}

	public function addRoutes($routes)
	{ 
        $routes = (array) KConfig::unbox($routes);
         
        foreach($routes as $uri => $query)
        {
        	$object = new stdclass;
        	$object->rule = $uri;
        	$object->uri = $this->compile($uri);
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
	public function build($query_string)
	{
		parse_str($query_string, $query);

		$uri = $this->getMatchingRoute($query);

		if (strpos($uri, '<') === FALSE AND strpos($uri, '[') === FALSE)
		{
			// This is a static route, no need to replace anything
			return $uri;
		}

		// Remove filters as they are used only in parsing
		$uri = preg_replace('#(\#[a-zA-Z0-9_]+)#', '', $uri);

		while (preg_match('#\[[^\[\]]++\]#', $uri, $match))
		{
			// Search for the matched value
			$search = $match[0];

			// Remove the square brackets from the match as the replace
			$replace = substr($match[0], 1, -1);

			while(preg_match('#'.SRouter::REGEX_KEY.'#', $replace, $match))
			{
				list($key, $param) = $match;

				if (isset($query[$param]))
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

			if ( ! isset($query[$param]))
			{
				// Ungrouped parameters are required
				throw new SRouterException('Required route parameter not passed');
			}

			$uri = str_replace($key, $query[$param], $uri);
			
			// Remove the param because it's already injected into the URI
			unset($query[$param]);
		}

		// Remove unnecessary slashes
		$uri = preg_replace('#//+#', '/', rtrim($uri, '/'));

		// Remove query parameters that are already in the defaults of the route

		if (count($query)) {
			$uri .= '?'. http_build_query($query);
		}

		return $uri;
	}

	/**
	 * Returns an array of variables parsed from the Route
	 *
	 */
	public function parse($route)
	{

	}

	public static function compile($uri, array $regex = NULL)
	{
		if ( ! is_string($uri))
			return;

		// The URI should be considered literal except for keys and optional parts
		// Escape everything preg_quote would escape except for : ( ) < >
		$expression = preg_replace('#'.SRouter::REGEX_ESCAPE.'#', '\\\\$0', $uri);

		if (strpos($expression, '[') !== FALSE)
		{
			// Make optional parts of the URI non-capturing and optional
			$expression = str_replace(array('[', ']'), array('[?:', ']?'), $expression);
		}

		// Insert default regex for keys
		$expression = str_replace(array('<', '>'), array('(?P<', '>'.SRouter::REGEX_PARAMETER.')'), $expression);

		if ($regex)
		{
			$search = $replace = array();
			foreach ($regex as $key => $value)
			{
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
	public function getMatchingRoute($query_string)
	{
		// @TODO: Cache the results

		if (!is_array($query_string)) {
			parse_str($query_string, $query);
		}
		else $query = $query_string;

		// We will match the query based on how similar they are
		$best_match = 'default';
		$similarities = array('default' => 0);

		foreach ($this->_routes as $route) 
		{
			$similarities[$route->rule] = $this->calculateSimilarity($route->query, $query);
			$best_match = ($similarities[$route->rule] > $similarities[$best_match]) ? $route->rule : $best_match;
		}

		return $best_match;
	}

	/**
	 * Calculate similarity based on how much keys and values match.
	 * 		Add one point for each similar key or value
	 *
	 */
	public function calculateSimilarity($base_string, $match_string)
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
				$points++;
				if ($match[$key] == $value) {
					$points++;
				}
			}
		}

		return $points;
	}
}