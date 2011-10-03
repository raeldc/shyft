<?php

class ComDefaultViewHtml extends KViewHtml
{
	protected $_page;
	protected $_mode;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_page = $config->page;
		$this->_mode = $config->mode;
	}
	
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'page' => KRequest::get('get.page','cmd'),
			'mode' => KRequest::get('get.mode','cmd', null),
		));
	
		parent::_initialize($config);
	}
	
	public function createRoute($route = '')
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

			// Parse route that we want to generate
			$parts = array();
			parse_str($route, $parts);

			if(isset($parts['com']))
			{
				// TODO: If com is specified, find the page that is attached to the component
			}
			else $parts['page'] = $this->_page;

			if (!empty($this->_mode)) {
				$parts['mode'] = $this->_mode;
			}

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

			$result = array();
			foreach($parts as $key => $value)
			{
				$result[] = $key.'='.$value;
			}

			return 'index.php?'.implode('&', $result);
		}

		return $result;
	}
}

