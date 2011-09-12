<?php
/**
 * @category	Flow
 * @package		Flow_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter for a component
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Flow
 * @package     Flow_Loader
 * @subpackage 	Adapter
 * @uses		KInflector
 */
class FlowLoaderAdapterComponent extends KLoaderAdapterComponent
{
	
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

		if (array_shift($parts) == 'com') 
		{
		    //Switch the basepath
		    if(!empty($basepath)) {
		        $this->_basepath = $basepath;
		    }
		    
		    $component = strtolower(array_shift($parts));
			$file 	   = array_pop($parts);

			if(count($parts)) 
			{
			    if($parts[0] != 'view') 
			    {
			        foreach($parts as $key => $value) {
					    $parts[$key] = KInflector::pluralize($value);
				    }
			    } 
			    else $parts[0] = KInflector::pluralize($parts[0]);
			    
				$path = implode('/', $parts);
				$path = $path.'/'.$file;
			} 
			else $path = $file;

			$path = Flow::findFile($component.'/'.$path.'.php', $this->_basepath);
		}
	
		return $path;
	}
}