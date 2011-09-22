<?php
/**
 * @category	Shyft
 * @package     Shyft_Database
 * @subpackage  Table
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.shyfted.com
 */

/**
 * Default Node Class for NoSQL Databases
 *
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Shyft
 * @package     Shyft_Database
 * @subpackage  Document
 */
class SDatabaseDocumentNodes extends SDatabaseDocumentAbstract
{
	protected function _initialize(KConfig $config)
	{
		// Use the node collection
		$config->append(array(
			'name' => 'nodes',
		));
	
		parent::_initialize($config);
	}
	
}