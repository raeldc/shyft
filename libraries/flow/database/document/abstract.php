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
 * Abstract Document Class for Document Oriented Databases
 *
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Flow
 * @package     Flow_Database
 * @subpackage  Document
 */
abstract class FlowDatabaseDocumentAbstract extends KObject implements KObjectIdentifiable
{
	protected $_collection;
	protected $_database;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		// TODO: Move this to an adapter
		if (!empty($config->username) && !empty($config->password)) 
		{
			$db = new Mongo('mongodb://'.$config->username.':'.$config->password.'@localhost');
		}
		else
		{
			$db = new Mongo();
		}

		$this->_database = $db->selectDB($config->database);

		$this->_collection = $this->_database->selectCollection($config->collection);
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'host' => 'localhost',
			'database' => 'flowku',
			'collection' => 'test',
			'username' => null,
			'password' => null
		));
	
		parent::_initialize($config);
	}

	public function getIdentifier()
	{
		return $this->_identifier;
	}

	public function find(FlowDatabaseQueryDocument $query, $mode = KDatabase::FETCH_ROWSET)
	{
		switch($mode)
        {
            case KDatabase::FETCH_ROW    : 
            {
                return $this->_collection->findOne((object)$query->query());
                break;
            }
            
            case KDatabase::FETCH_ROWSET : 
            {
                return $this->_collection->find((object)$query->query());
                break;
            }
        }
	}

	public function getQuery()
	{
		static $instance;

		if (is_null($instance)) {
			$instance = new FlowDatabaseQueryDocument();
		}

		return $instance;
	}
}