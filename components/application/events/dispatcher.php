<?php
/**
 * @category	Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.shyfted.com
 */


/**
 * Default Event Dispatcher
.*
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Shyft
 * @package     Shyft_Components
 * @subpackage  Application
 */
class ComApplicationEventDispatcher extends KEventDispatcher implements KServiceInstantiatable
{
 	/**
     * Force creation of a singleton
     *
     * @param 	object	An optional KConfig object with configuration options
     * @param 	object	A KServiceInterface object
     * @return ComDefaultEventDispatcher
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

            //Add the factory map to allow easy access to the singleton
            $container->setAlias('koowa:event.dispatcher', $config->service_identifier);
        }

        return $container->get($config->service_identifier);
    }
}