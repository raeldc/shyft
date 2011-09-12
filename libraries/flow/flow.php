<?php
/**
* @category		Flow
* @copyright    Copyright (C) 2011 Israel Canasa. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link         http://www.flowku.com
*/

/**
 * Flow class
 *
 * Provides metadata for Flow such as version info
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @package     Flow
 */
class Flow
{
    /**
     * Flow version
     * 
     * @var string
     */
    const VERSION = '0.1-prototype';
    
    /**
     * Path to Flow libraries
     */
    protected static $_path;

    /**
     * Container for singleton self instance
     */
    public static $instance;

    /**
     * Container for paths to essential directories
     */
    public static $paths;

    public function __construct($paths)
    {
        // Don't do anything else if instance is already created.
        if (self::$instance !== null)
            return false;

        self::$paths['libraries']   = (isset($paths['libraries']))  ? $paths['libraries']   : SYSTEM_LIBRARIES;
        self::$paths['components']  = (isset($paths['components'])) ? $paths['components']  : SYSTEM_COMPONENTS;
        self::$paths['actions']     = (isset($paths['actions']))    ? $paths['actions']     : SYSTEM_ACTIONS;
        self::$paths['templates']   = (isset($paths['templates']))  ? $paths['templates']   : SYSTEM_TEMPLATES;
        self::$paths['languages']   = (isset($paths['languages']))  ? $paths['languages']   : SYSTEM_LANGUAGES;

        // Get Koowa, for some reason, this serves nothing critical
        require_once self::findFile('koowa/koowa.php',                    self::$paths['libraries']);

        // Exception Classes
        require_once self::findFile('koowa/exception/interface.php',      self::$paths['libraries']);
        require_once self::findFile('koowa/exception/exception.php',      self::$paths['libraries']);

        // Loader Classes
        require_once self::findFile('koowa/loader/adapter/interface.php', self::$paths['libraries']);
        require_once self::findFile('koowa/loader/adapter/exception.php', self::$paths['libraries']);
        require_once self::findFile('koowa/loader/adapter/abstract.php',  self::$paths['libraries']);
        require_once self::findFile('flow/loader/adapter/koowa.php',      self::$paths['libraries']);
        require_once self::findFile('flow/loader/adapter/flow.php',       self::$paths['libraries']);

        // Registry Classes
        require_once self::findFile('koowa/loader/registry.php',          self::$paths['libraries']);

        // Get Flow Loader
        require_once self::findFile('flow/loader/loader.php',             self::$paths['libraries']);

        /*
         *      We have a special loader adapter for Flow which looks for "fallback" directories
         *      Fallback directories is searched in this order sites/site -> sites/all -> system
         */

        // Register the necessary Loader Adapters
        FlowLoader::registerAdapter(new FlowLoaderAdapterKoowa(self::$paths['libraries']));
        FlowLoader::registerAdapter(new FlowLoaderAdapterFlow(self::$paths['libraries']));
        FlowLoader::registerAdapter(new FlowLoaderAdapterComponent(self::$paths['components']));
        FlowLoader::registerAdapter(new FlowLoaderAdapterAction(self::$paths['actions']));

        // Register the necessary Identifier Adapaters
        KIdentifier::registerAdapter(new FlowIdentifierAdapterKoowa());
        KIdentifier::registerAdapter(new FlowIdentifierAdapterFlow());
        KIdentifier::registerAdapter(new FlowIdentifierAdapterComponent());
    }

    /**
     * Get the version of the Flow library
     */
    public static function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Get path to Flow libraries
     */
    public static function getPath()
    {
        if(!isset(self::$_path)) {
            self::$_path = dirname(__FILE__);
        }

        return self::$_path;
    }

    public static function getInstance($directories = array())
    {
        if (self::$instance === null) {
            self::$instance = new self($directories);
        }
        
        return self::$instance;
    }

    /**
     * Search for a file in the given array of directories unti it is found
     *
     * @return string   Real path to the file
     */
    public function findFile($path, $directories)
    {
        if (is_string($directories)) 
        {
            return rtrim($directories, '/').'/'.trim($path, '/');
        }

        foreach ($directories as $value) 
        {
            $file = array_shift($directories).'/'.trim($path, '/');
            if (is_file($file)) return $file;
        }

        return false;
    }
}