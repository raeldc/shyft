<?php
/**
 * @category	Flow
 * @package		Flow_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter for a plugin
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Flow
 * @package     Flow_Loader
 * @subpackage 	Adapter
 * @uses		KInflector
 */
class FlowLoaderAdapterAction extends KLoaderAdapterAbstract
{
	/** 
	 * The adapter type
	 * 
	 * @var string
	 */
	protected $_type = 'act';
	
	/**
	 * The class prefix
	 * 
	 * @var string
	 */
	protected $_prefix = 'Action';
	
	/**
	 * Get the path based on a class name
	 *
	 * @param  string		  	The class name 
	 * @return string|false		Returns the path on success FALSE on failure
	 */
	public function findPath($classname, $basepath = null)
	{	
		$path = false; 
		
		$word  = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $classname));
		$parts = explode('_', $word);
			
		if (array_shift($parts) == 'action') 
		{
		    //Switch the basepath
		    if(!empty($basepath)) {
		        $this->_basepath = $basepath;
		    }

		    $type = strtolower(array_shift($parts));

		    $action = strtolower(array_shift($parts));
			$file 	   = array_pop($parts);
				
			if(count($parts)) 
			{		
				$path = implode('/', $parts);
				$path = $path.'/'.$file;
			} 
			else $path = $file;

			if (($file = Flow::findFile($type.'/'.$action.'/'.$action.'.php', $this->_basepath)) === false) {
			    $path = Flow::findFile($type.'/'.$action.'.php', $this->_basepath);
		    }
		    else $path = $file;
		}

		return $path;
		
	}
}