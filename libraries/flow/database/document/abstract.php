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
           
        // TODO: Set the column filters
        if(!empty($config->filters) && false) 
        {
            foreach($config->filters as $column => $filter) {
                $this->getColumn($column, true)->filter = KConfig::toData($filter);
            }       
        }
    
        // Mixin a command chain
         $this->mixin(new KMixinCommandchain($config->append(array('mixer' => $this))));
           
        // Set the table behaviors
        if(!empty($config->behaviors)) {
            $this->addBehavior($config->behaviors);
        } 
	}

	protected function _initialize(KConfig $config)
	{
		// TODO: Set the database to be a singleton, use com:application.database
		$database = KFactory::get('flow:database.adapter.document');
		$package = $this->_identifier->package;
        $name    = $this->_identifier->name;

		$config->append(array(
			'command_chain'     => new KCommandChain(),
			'command_chain'     => new KCommandChain(),
            'dispatch_events'   => false,
            'enable_callbacks'  => false,

			'database' 			=> $database,
			'behaviors' 		=> array(),
			'filters'	 		=> array(),
			'name' 				=> empty($package) ? $name : $package.'_'.$name,
		));
	
		parent::_initialize($config);
	}

	public function getIdentifier()
	{
		return $this->_identifier;
	}

	public function find(FlowDatabaseQueryDocument $query, $mode = KDatabase::FETCH_ROWSET)
	{
        //Create commandchain context
        $context = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_SELECT;
        $context->query     = $query;
        $context->mode      = $mode;
        
        if($this->getCommandChain()->run('before.find', $context) !== false) 
        {
            switch($context->mode)
            {
                case KDatabase::FETCH_ROW    : 
                {
                	$data = $this->getDocument()->findOne((object)$context->query->query());
                    $context->data = $this->getRow();

                    if(isset($data) && !empty($data)) {
                       $context->data->setData(iterator_to_array($data), false)->setStatus(KDatabase::STATUS_LOADED);
                    }
                    break;
                }
                
                case KDatabase::FETCH_ROWSET : 
                {
                	$data = $this->getDocument()->find((object)$context->query->query());
                    $context->data = $this->getRowset();

                    if(isset($data) && !empty($data)) {
                        $context->data->addData(iterator_to_array($data), false);
                    }
                    break;
                }
                
                default : $context->data = $data;
            }
                        
            $this->getCommandChain()->run('after.find', $context);
        }
    
        return KConfig::toData($context->data);
	}

	/**
     * Count table rows
     *
     * @param   mixed   KDatabaseQuery object or query string or null to count all rows
     * @return  int     Number of rows
     */
    public function count($query = null)
    {
        return $this->getDocument()->find((object)$query->query())->count();
    }

	/**
     * Get an instance of a row object for this table
     *
     * @param	array An optional associative array of configuration settings.
     * @return  KDatabaseRowInterface
     */
    public function getRow(array $options = array())
    {
        $identifier         = clone $this->_identifier;
        $identifier->path   = array('database', 'row');
        $identifier->name   = KInflector::singularize($this->_identifier->name);
            
        //The row default options
        $options['document'] = $this; 
             
        return KFactory::get($identifier, $options); 
    }

    /**
     * Get an instance of a rowset object for this table
     *
     * @param	array An optional associative array of configuration settings.
     * @return  KDatabaseRowInterface
     */
    public function getRowset(array $options = array())
    {
        $identifier         = clone $this->_identifier;
        $identifier->path   = array('database', 'rowset');
            
        //The rowset default options
        $options['document'] = $this;

        return KFactory::get($identifier, $options);
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

	/**
     * Register one or more behaviors to the table
     *
     * @param   array   Array of one or more behaviors to add.
     * @return  KDatabaseTableAbstract
     */
    public function addBehavior($behaviors)
    {
        $behaviors = (array) KConfig::toData($behaviors);
                
        foreach($behaviors as $behavior)
        {
            if (!($behavior instanceof KDatabaseBehaviorInterface)) { 
                $behavior   = $this->getBehavior($behavior); 
            } 
              
		    // TODO: Add the behavior
            // $this->getSchema()->behaviors[$behavior->getIdentifier()->name] = $behavior;
            $this->getCommandChain()->enqueue($behavior);
        }
        
        return $this;
    }
}