<?php
/**
 * @category	Flow
 * @package		Flow_Identifier
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Identifier Adapter for the Koowa framework
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Identifier
 * @subpackage 	Adapter
 * @uses 		KInflector
 */
class FlowIdentifierAdapterKoowa extends KIdentifierAdapterKoowa
{	
	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	Identifier or Identifier object - koowa.[.path].name
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

		$path = Flow::findFile($path.'.php', $identifier->basepath);
		return $path;
	}
}