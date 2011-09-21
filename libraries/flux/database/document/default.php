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
 * Abstract Table Class for NoSQL Databases
 *
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Flux
 * @package     Flux_Database
 * @subpackage  Table
 */
class FluxDatabaseDocumentDefault extends FluxDatabaseDocumentAbstract implements KObjectInstantiatable
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
     * @return FluxDatabaseDocumentDefault
     */
    public static function getInstance($config, KFactoryInterface $factory)
    {
       // Check if an instance with this identifier already exists or not
        if (!$factory->exists($config->identifier))
        {
            //Create the singleton
            $classname = $config->identifier->classname;
            $instance  = new $classname($config);
            $factory->set($config->identifier, $instance);
        }
        
        return $factory->get($config->identifier);
    }
}