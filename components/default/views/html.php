<?php

class ComDefaultViewHtml extends KViewHtml
{
	public function createRoute( $route = '')
	{
		$route = trim($route);

		// Special cases
		if($route == 'index.php' || $route == 'index.php?') 
		{
			$result = $route;
		} 
		else if (substr($route, 0, 1) == '&') 
		{
			$url   = clone KRequest::url();
			$vars  = array();
			parse_str($route, $vars);
			
			$url->setQuery(array_merge($url->getQuery(true), $vars));
			
			$result = 'index.php?'.$url->getQuery();
		}
		else 
		{
			// Strip 'index.php?'
			if(substr($route, 0, 10) == 'index.php?') {
				$route = substr($route, 10);
			}

			// Parse route
			$parts = array();
			parse_str($route, $parts);
			$result = array();

			// Check to see if there is component information in the route if not add it
			if(!isset($parts['com'])) {
				$result[] = 'com='.$this->_identifier->package;
			}

			// Add the layout information to the route only if it's not 'default'
			if(!isset($parts['view']))
			{
				$result[] = 'view='.$this->getName();
				if(!isset($parts['layout']) && $this->_layout != $this->_layout_default) {
					$result[] = 'layout='.$this->getLayout();
				}
			}
			
			// Add the format information to the URL only if it's not 'html'
			if(!isset($parts['format']) && $this->_identifier->name != 'html') {
				$result[] = 'format='.$this->_identifier->name;
			}

			// Reconstruct the route
			if(!empty($route)) {
				$result[] = $route;
			}

			$result = 'index.php?'.implode('&', $result);
			
		}

		return $result;
	}
}

