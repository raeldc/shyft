<?php
/**
 * @category	Flowku
 * @package     Flowku_Widgets
 * @subpackage  Application
 * @copyright   Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.flowku.com
 */

/**
 * Default Template for widgets
.*
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Flowku
 * @package     Flowku_Widgets
 * @subpackage  Default
 */
class WidgetDefaultTemplateDefault extends ComDefaultTemplateDefault
{
	/**
	 * Load a template by identifier
	 * 
	 * This functions only accepts full identifiers of the format
	 * -  com:[//application/]component.view.[.path].name
	 *
	 * This tries to convert an component identifier into the application's theme identifier to look for overrides
	 *
	 * @param   string 	The template identifier
	 * @param	array	An associative array of data to be extracted in local template scope
	 * @param	boolean	If TRUE process the data using a tmpl stream. Default TRUE.
	 * @return KTemplateAbstract
	 */
	public function loadIdentifier($template, $data = array(), $process = true)
	{
	    //Identify the template
	    $identifier = KIdentifier::identify($template);
	    $file = $identifier->filepath;

	    if ($identifier->type == 'widget') 
	    {
	    	$theme = clone KFactory::get('theme')->getLayout();

	    	$path = array();
	    	if (!empty($identifier->path) && $identifier->path[0] == 'view')
	    	{
	    		$path = $identifier->path;
	    		array_shift($path);
	    	}

	    	$theme->path = array_merge(array('html', 'widgets', $identifier->package), $path);
	    	$theme->name = $identifier->name;

	    	$file = $theme->filepath;

	    	// Now try the widget
		    if ($file === false) 
		    {
		    	array_push($identifier->path, 'tmpl');
		    	$file = $identifier->filepath;
		    }
	    }

		if ($file === false) {
			throw new KTemplateException('Template "'.$identifier.'" not found');
		}
		
		// Load the file
		$this->loadFile($file, $data, $process);
		
		return $this;
	}
}