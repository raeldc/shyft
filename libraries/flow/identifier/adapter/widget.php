<?php
/**
 * @category	Flow
 * @package		Flow_Identifier
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Identifier Adapter for a component
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Flow
 * @package     Flow_Identifier
 * @subpackage 	Adapter
 */
class FlowIdentifierAdapterWidget extends KIdentifierAdapterAbstract
{
	/** 
	 * The adapter type
	 * 
	 * @var string
	 */
	protected $_type = 'widget';
	
	/**
	 * Get the classname based on an identifier
	 * 
	 * This factory will try to create an generic or default classname on the identifier information
	 * if the actual class cannot be found using a predefined fallback sequence.
	 * 
	 * Fallback sequence : -> Named Component Specific
	 *                     -> Named Component Default  
	 *                     -> Default Component Specific
	 *                     -> Default Component Default
	 *                     -> Framework Specific 
	 *                     -> Framework Default
	 *
	 * @param mixed  		 Identifier or Identifier object - com:[//application/]component.view.[.path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KIdentifier $identifier)
	{ 
	    $path      = KInflector::camelize(implode('_', $identifier->path));
        $classname = 'Widget'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
        
        //Don't proceed if trying to load a controller
		if (is_array($identifier->path) && !empty($identifier->path) && in_array($identifier->path[0], array('controller')))
			return false;

      	//Manually load the class to set the basepath
		if (!FlowLoader::loadClass($classname, $identifier->basepath))
		{
			$classname = false;
		    $classpath = $identifier->path;
			$classtype = !empty($classpath) ? array_shift($classpath) : '';
					
			//Create the fallback path and make an exception for views
			$path = ($classtype != 'view') ? KInflector::camelize(implode('_', $classpath)) : '';

			/*
			 * Find the classname to fallback too and auto-load the class
			 * 
			 * Fallback sequence : -> Named Widget Specific 
			 *                     -> Named Widget Default  
			 *                     -> Named Component Specific 
			 *                     -> Named Component Default
			 *                     -> Flow Specific
			 *                     -> Flow Default
			 *                     -> Framework Specific 
			 *                     -> Framework Default
			 */

			if(class_exists('Widget'.ucfirst($identifier->package).ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'Widget'.ucfirst($identifier->package).ucfirst($classtype).$path.ucfirst($identifier->name);
			}elseif(class_exists('WidgetDefault'.ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'WidgetDefault'.ucfirst($classtype).$path.ucfirst($identifier->name);
			}elseif(class_exists('WidgetDefault'.ucfirst($classtype).$path.'Default')) {
				$classname = 'WidgetDefault'.ucfirst($classtype).$path.'Default';
			}

			// Return if classname now has value
			if($classname) return $classname;

			// Don't proceed if the classtype is view because we only want to use the widget's view.
			if($classtype == 'view') {
				throw new KLoaderException('View : '.$identifier.' not found');
			}

			// Proceed on loading other stuffs on the component and framework
			if(class_exists('Com'.ucfirst($identifier->package).ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'Com'.ucfirst($identifier->package).ucfirst($classtype).$path.ucfirst($identifier->name);
			}elseif(class_exists('Com'.ucfirst($identifier->package).ucfirst($classtype).$path.'Default')) {
				$classname = 'Com'.ucfirst($identifier->package).ucfirst($classtype).$path.'Default';
			} elseif(class_exists('ComDefault'.ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'ComDefault'.ucfirst($classtype).$path.ucfirst($identifier->name);
			} elseif(class_exists('ComDefault'.ucfirst($classtype).$path.'Default')) {
				$classname = 'ComDefault'.ucfirst($classtype).$path.'Default';
			} elseif(class_exists( 'Flow'.ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'Flow'.ucfirst($classtype).$path.ucfirst($identifier->name);
			}elseif(class_exists( 'Flow'.ucfirst($classtype).$path.ucfirst($identifier->name).'Default')) {
				$classname = 'Flow'.ucfirst($classtype).$path.ucfirst($identifier->name).'Default';
			}elseif(class_exists('Flow'.ucfirst($classtype).$path.'Default')) {
				$classname = 'Flow'.ucfirst($classtype).$path.'Default';
			}elseif(class_exists( 'K'.ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'K'.ucfirst($classtype).$path.ucfirst($identifier->name);
			}elseif(class_exists( 'K'.ucfirst($classtype).$path.ucfirst($identifier->name).'Default')) {
				$classname = 'K'.ucfirst($classtype).$path.ucfirst($identifier->name).'Default';
			} elseif(class_exists('K'.ucfirst($classtype).$path.'Default')) {
				$classname = 'K'.ucfirst($classtype).$path.'Default';
			} else {
				$classname = false;
			}
		}

		return $classname;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	An Identifier object - com:[//application/]component.view.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KIdentifier $identifier)
	{
        $path  = '';
	    $parts = $identifier->path;
				
		$component = strtolower($identifier->package);
			
		if(!empty($identifier->name))
		{
			if(count($parts)) 
			{
				$path    = KInflector::pluralize(array_shift($parts));
				$path   .= count($parts) ? '/'.implode('/', $parts) : '';
				$path   .= '/'.strtolower($identifier->name);	
			} 
			else $path  = strtolower($identifier->name);	
		}

		$path = Flow::findFile('/widgets/'.$component.'/'.$path.'.php', $identifier->basepath);

		return $path;
	}
}