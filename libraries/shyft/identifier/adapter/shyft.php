<?php
/**
 * @category	Shyft
 * @package		Shyft_Factory
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         http://www.shyfted.com
 */

/**
 * Factory Adapter for the Shyft framework
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Shyft
 * @package     Shyft_Factory
 * @subpackage 	Adapter
 * @uses 		KInflector
 */
class SIdentifierAdapterShyft extends KIdentifierAdapterAbstract
{
	/** 
	 * The adapter type
	 * 
	 * @var string
	 */
	protected $_type = 'shyft';
	
	/**
	 * Get the classname based on an identifier
	 *
	 * @param 	mixed  		 Identifier or Identifier object - shyft.[.path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KIdentifier $identifier)
	{
        $classname = 'S'.ucfirst($identifier->package).KInflector::implode($identifier->path).ucfirst($identifier->name);
			
		if (!class_exists($classname))
		{
			// use default class instead
			$classname = 'S'.ucfirst($identifier->package).KInflector::implode($identifier->path).'Default';
				
			if (!class_exists($classname)) {
				$classname = false;
			}
		}
		
		return $classname;
	}
	
	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	Identifier or Identifier object - shyft.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KIdentifier $identifier)
	{
		if(count($identifier->path)) {
			$path .= implode('/',$identifier->path);
		}

		if(!empty($identifier->name)) {
			$path .= '/'.$identifier->name;
		}
				
		$path = $identifier->basepath.'/'.$path.'.php';
		return $path;
	}
}