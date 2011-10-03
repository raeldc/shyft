<?php
/**
 * @category	Shyft
 * @package		Shyft_Identifier
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Identifier Adapter for a theme
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Shyft
 * @package     Shyft_Identifier
 * @subpackage 	Adapter
 */
class SServiceLocatorTheme extends KServiceLocatorAbstract
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
	public function findClass(KServiceIdentifier $identifier)
	{ 
	    return false;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	An Identifier object - com:[//application/]component.view.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KServiceIdentifier $identifier)
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

		$path = Shyft::findFile('/themes/'.$theme.$path.'/'.$layout.'.php', $identifier->basepath);

		return $path;
	}
}