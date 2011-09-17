<?php
/**
 * @category	Flow
 * @package		Flow_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter for a widget
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Flow
 * @package     Flow_Loader
 * @subpackage 	Adapter
 * @uses		KInflector
 */
class FlowLoaderAdapterWidget extends KLoaderAdapterAbstract
{
	/** 
	 * The adapter type
	 * 
	 * @var string
	 */
	protected $_type = 'widget';
	
	/**
	 * The class prefix
	 * 
	 * @var string
	 */
	protected $_prefix = 'Widget';
	
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

		if (array_shift($parts) == 'widget') 
		{
		    $widget = strtolower(array_shift($parts));
			$file 	   = array_pop($parts);

			//Merge the basepath
			if (is_array($basepath)) 
			{
				$basepaths = $this->_basepath;
				foreach ($basepaths as $base) {
					// We need to do this so that it won't search for widgets/widgets folder.
					$basepaths[] = rtrim($base, 'widgets').'widgets';
				}

				$basepath = array_unique($basepaths);
			}
			else $basepath = $this->_basepath;

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
			elseif($file) $path = $file;
			else $path = $widget;

			$path = Flow::findFile($widget.'/'.$path.'.php', $basepath);
		}

		return $path;
	}
}