<?php
/**
 * @version        $Id$
 * @package        Koowa_Dispatcher
 * @subpackage  Session
 * @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         http://www.nooku.org
 */

/**
 * Default Session Class
 *
 * @author        Johan Janssens <johan@nooku.org>
 * @package     Koowa_Dispatcher
 * @subpackage  Session
 */
class KDispatcherSessionDefault extends KDispatcherSessionAbstract implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param     object     $config    An optional KConfig object with configuration options
     * @param     object    $container A KServiceInterface object
     * @return KDispatcherSessionDefault
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }
}