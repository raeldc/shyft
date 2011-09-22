<?php
/**
 * @category	Flux
 * @package		Flux_Identifier
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Identifier Adapter for a plugin
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Flux
 * @package     Flux_Identifier
 * @subpackage 	Adapter
 * @uses 		KInflector
 * @uses 		Flux
 */
class FluxIdentifierAdapterAction extends KIdentifierAdapterAbstract
{
    /** 
	 * The adapter type
	 * 
	 * @var string
	 */
	protected $_type = 'act';
    
	/**
	 * Get the classname based on an identifier
	 * 
	 * @param  mixed  		 Identifier or Identifier object - plg.type.plugin.[.path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KIdentifier $identifier)
	{
	    $classpath = KInflector::camelize(implode('_', $identifier->path));
		$classname = 'Action'.ucfirst($identifier->package).$classpath.ucfirst($identifier->name);
		
		//Don't allow the auto-loader to load plugin classes if they don't exists yet
		if (!class_exists( $classname)) {
			$classname = false;
		}

		return $classname;
	}
	
	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  			An Identifier object - plg.type.plugin.[.path].name
	 * @return string|false		Returns the path on success FALSE on failure
	 */
	public function findPath(KIdentifier $identifier)
	{
	    $parts = $identifier->path;
			
		$action  = array_shift($parts);
		$type  = $identifier->package;
			
		if(!empty($identifier->name))
		{
			if(count($parts)) 
			{
				$path    = array_shift($parts).
				$path   .= count($parts) ? '/'.implode('/', $parts) : '';
				$path   .= '/'.strtolower($identifier->name);	
			} 
			else $path  = strtolower($identifier->name);	
		}
		
		//Plugins can have their own folder
		if (($path = Flux::findFile($type.'/'.$action.'/'.$path.'.php', $identifier->basepath)) === false) {
		    $path = Flux::findFile($type.'/'.$action.'/'.$path.'/'.$path.'.php', $identifier->basepath);
	    }

		return $path;
	}
}