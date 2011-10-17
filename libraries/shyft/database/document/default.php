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
 * Abstract Table Class for NoSQL Databases
 *
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Shyft
 * @package     Shyft_Database
 * @subpackage  Table
 */
class SDatabaseDocumentDefault extends SDatabaseDocumentAbstract implements KServiceInstantiatable
{
    protected function _initialize(KConfig $config)
    {
        if (!isset($config->identity_column)) {
            $config->identity_column = '_id';
        }

        parent::_initialize($config);
    }
    
	/**
     * Force creation of a singleton
     *
     * @return SDatabaseDocumentDefault
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }
        
        return $container->get($config->service_identifier);
    }
}