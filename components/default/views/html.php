<?php

class ComDefaultViewHtml extends KViewHtml
{
	protected $_route;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_route = $config->route;
	}
	
	public function createRoute($route = '')
	{
		$route = trim($route);

		// Special cases
		if($route == 'index.php' || $route == 'index.php?') 
		{
			return array('base' => true);
		}
		elseif (substr($route, 0, 10) == 'index.php?') 
		{
			parse_str(substr($route, 10), $query);
			$query['base'] = true;
			$result = $this->getService('com://site/application.router')->build($query);
		}elseif (substr($route, 0, 1) == '&') 
		{
			parse_str($route, $query);
			$query['base'] = true;
			$result = $this->getService('com://site/application.router')->build($query);
		}
		else 
		{
			// Strip 'index.php?'
			if(substr($route, 0, 10) == 'index.php?') {
				$route = substr($route, 10);
			}

			// Parse route that we want to generate
			parse_str($route, $parts);

			unset($parts['base']);

			// Add the layout information to the route only if it's not 'default'
			if(!isset($parts['view']))
			{
				$parts['view'] = $this->getName();
				if(!isset($parts['layout']) && $this->_layout != $this->_layout_default) {
					$parts['layout'] = $this->getLayout();
				}
			}

			// Add the format information to the URL only if it's not 'html'
			if(!isset($parts['format']) && $this->getIdentifier()->name != 'html') {
				$parts['format'] = $this->getIdentifier()->name;
			}

			$result = $this->getService('com://site/application.router')->build($parts);
		}

		return $result;
	}
}

