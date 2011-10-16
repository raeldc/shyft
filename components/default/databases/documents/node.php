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
class ComDefaultDatabaseDocumentNode extends SDatabaseDocumentDefault
{
	protected $_type;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_type = $config->type;
	}

	protected function _initialize(KConfig $config)
	{
		// Add these behaviors only if the behaviors are not manually set
		if (!isset($config->behaviors)) {
			$config->behaviors = array('pageable', 'typable');
		}

		// Get the type name from the package name and class name
		$config->append(array(
			'type' => $this->getIdentifier()->package.'_'.$this->getIdentifier()->name,
		));

		// Force the use of "contents" as the collection name
		$config->name = 'contents';

		parent::_initialize($config);
	}

	public function getType()
	{
		return $this->_type;
	}
}