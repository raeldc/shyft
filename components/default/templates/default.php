<?php
/**
 * @category	Flowku
 * @package     Flowku_Components
 * @subpackage  Application
 * @copyright   Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.flowku.com
 */

/**
 * Default Template
.*
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Flowku
 * @package     Flowku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateDefault extends KTemplateAbstract
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

	    //TODO: Suggest to Johan that map identifiers should also be checked by KFactory::exists()
	    if ($identifier->type == 'com') {
	    	$theme = clone KFactory::get('theme')->getLayout();

	    	$path = array();
	    	if (!empty($identifier->path) && $identifier->path[0] == 'view')
	    	{
	    		$path = $identifier->path;
	    		array_shift($path);
	    	}

	    	$theme->path = array_merge(array('html', 'components', $identifier->package), $path);
	    	$theme->name = $identifier->name;

	    	$file = $theme->filepath;

	    	// Now try the component
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

	/**
     * Load a template helper
     * 
     * This function merges the elements of the attached view model state with the parameters passed to the helper
     * so that the values of one are appended to the end of the previous one. 
     * 
     * If the view state have the same string keys, then the parameter value for that key will overwrite the state.
     *
     * @param   string  Name of the helper, dot separated including the helper function to call
     * @param   mixed   Parameters to be passed to the helper
     * @return  string  Helper output
     */
    public function renderHelper($identifier, $params = array())
    {
        $view = $this->getView();
        
        if(KInflector::isPlural($view->getName())) 
        {
            if($state = $view->getModel()->getState()) {
                $params = array_merge( $state->getData(), $params);
            }
        } 
        else 
        {
            if($item = $view->getModel()->getItem()) {
                $params = array_merge( $item->getData(), $params);
            }
        }   
        
        return parent::renderHelper($identifier, $params);
    }
}