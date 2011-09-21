<?php
/**
 * @category	Flow
 * @package		Flow_Identifier
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Identifier Adapter for a theme
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Flow
 * @package     Flow_Identifier
 * @subpackage 	Adapter
 */
class FlowIdentifierAdapterTheme extends KIdentifierAdapterAbstract
{
	/** 
	 * The adapter type
	 * 
	 * @var string
	 */
	protected $_type = 'theme';
	
	/**
	 * Not Applicable for a theme
	 *
	 * @return string|false  Return FALSE
	 */
	public function findClass(KIdentifier $identifier)
	{ 
	    return false;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	An Identifier object - com:[//application/]component.view.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KIdentifier $identifier)
	{
		if(empty($identifier->application)) {
			throw new KIdentifierAdapterException("Application not specified in theme: " . $identifier);
		}
		
		$path = '';
	    $parts = $identifier->path;
				
		$theme = strtolower($identifier->package);
		$layout   = strtolower($identifier->name);	

		if (!empty($parts)) {
			$path = '/'.implode('/', $parts);
		}

		$path = Flow::findFile('/themes/'.$theme.$path.'/'.$layout.'.php', $identifier->basepath);

		return $path;
	}
}