<?php
/**
 * @category	Flow
 * @package     Flow_Database
 * @subpackage  Table
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.flowku.com
 */

/**
 * Default Node Class for NoSQL Databases
 *
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Flow
 * @package     Flow_Database
 * @subpackage  Document
 */
class FlowDatabaseDocumentNodes extends FlowDatabaseDocumentAbstract
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