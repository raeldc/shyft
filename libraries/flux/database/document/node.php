<?php
/**
 * @category	Flux
 * @package     Flux_Database
 * @subpackage  Table
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.fluxed.com
 */

/**
 * Default Node Class for NoSQL Databases
 *
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Flux
 * @package     Flux_Database
 * @subpackage  Document
 */
class FluxDatabaseDocumentNodes extends FluxDatabaseDocumentAbstract
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