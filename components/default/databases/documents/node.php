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
 * The table for all content types using the contents collection.
 *
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Shyft
 * @package     Shyft_Database
 * @subpackage  Document
 */
class ComDefaultDatabaseDocumentNode extends SDatabaseDocumentAbstract
{
	protected function _initialize(KConfig $config)
	{
		$config->name = 'contents';

		parent::_initialize($config);
	}
}