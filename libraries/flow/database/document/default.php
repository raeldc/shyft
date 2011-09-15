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
 * Abstract Table Class for NoSQL Databases
 *
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Flow
 * @package     Flow_Database
 * @subpackage  Table
 */
class FlowDatabaseDocumentDefault extends FlowDatabaseDocumentAbstract implements KObjectInstantiatable
{
	/**
     * Associative array of table instances
     * 
     * @var array
     */
    private static $_instances = array();
    
	/**
     * Force creation of a singleton
     *
     * @return KDatabaseDocumentDefault
     */
    public static function getInstance($config = array())
    {
        // Check if an instance with this identifier already exists or not
        $instance = (string) $config->identifier;
        if (!isset(self::$_instances[$instance]))
        {
            //Create the singleton
            $classname = $config->identifier->classname;
            self::$_instances[$instance] = new $classname($config);
        }
        
        return self::$_instances[$instance];
    }
}