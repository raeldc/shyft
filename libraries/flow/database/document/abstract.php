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
	protected $_database;
	protected $_document;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_database = $config->database;
		$this->_document = $config->name;
	}

	protected function _initialize(KConfig $config)
	{
		// TODO: Set the database to be a singleton, use com:application.database
		$database = KFactory::get('flow:database.adapter.document');
		$package = $this->_identifier->package;
        $name    = $this->_identifier->name;

		$config->append(array(
			'database' => $database,
			'behaviors' => array(),
			'name' => empty($package) ? $name : $package.'_'.$name,
		));
	
		parent::_initialize($config);
	}

	public function getIdentifier()
	{
		return $this->_identifier;
	}

	public function find(FlowDatabaseQueryDocument $query, $mode = KDatabase::FETCH_ROWSET)
	{
		// TODO : Select document based on the table settings.
		switch($mode)
        {
            case KDatabase::FETCH_ROW    : 
            {
                return $this->getDocument()->findOne((object)$query->query());
                break;
            }
            
            case KDatabase::FETCH_ROWSET : 
            {
                return $this->getDocument()->find((object)$query->query());
                break;
            }
        }
	}

	public function getDocument()
	{
		if (!($this->_document instanceof MongoCollection)) {
			$this->_document = $this->_database->selectCollection($this->_document);
		}

		return $this->_document;
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