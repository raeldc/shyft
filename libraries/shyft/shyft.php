<?php
/**
* @category		Shyft
* @copyright    Copyright (C) 2011 Israel Canasa. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link         http://www.shyfted.com
*/

/**
 * Shyft class
 *
 * Provides metadata for Shyft such as version info
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @package     Shyft
 */
class Shyft
{
    /**
     * Shyft version
     * 
     * @var string
     */
    const VERSION = '0.1-prototype';
    
    /**
     * Path to Shyft libraries
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

    final public function __construct($paths)
    {
        // Don't do anything else if instance is already created.
        if (self::$instance !== null)
            return false;

        self::$paths['libraries']   = (isset($paths['libraries']))  ? $paths['libraries']   : SYSTEM_LIBRARIES;
        self::$paths['components']  = (isset($paths['components'])) ? $paths['components']  : SYSTEM_COMPONENTS;
        self::$paths['widgets']     = (isset($paths['widgets']))    ? $paths['widgets']     : SYSTEM_WIDGETS;
        self::$paths['actions']     = (isset($paths['actions']))    ? $paths['actions']     : SYSTEM_ACTIONS;
        self::$paths['themes']      = (isset($paths['themes']))     ? $paths['themes']      : SYSTEM_THEMES;
        self::$paths['languages']   = (isset($paths['languages']))  ? $paths['languages']   : SYSTEM_LANGUAGES;
        self::$paths['site']        = (isset($paths['site']))       ? $paths['site']        : SYSTEM_ROOT;

        // Get Koowa, for some reason, this serves nothing critical
        require_once self::findFile('koowa/koowa.php',                    self::$paths['libraries']);

        // Exception Classes
        require_once self::findFile('koowa/exception/interface.php',      self::$paths['libraries']);
        require_once self::findFile('koowa/exception/exception.php',      self::$paths['libraries']);

        // Loader Classes
        require_once self::findFile('koowa/loader/adapter/interface.php', self::$paths['libraries']);
        require_once self::findFile('koowa/loader/adapter/exception.php', self::$paths['libraries']);
        require_once self::findFile('koowa/loader/adapter/abstract.php',  self::$paths['libraries']);
        require_once self::findFile('shyft/loader/adapter/koowa.php',      self::$paths['libraries']);
        require_once self::findFile('shyft/loader/adapter/shyft.php',       self::$paths['libraries']);

        // Registry Classes
        require_once self::findFile('koowa/loader/registry.php',          self::$paths['libraries']);

        // Get Shyft Loader
        require_once self::findFile('shyft/loader/loader.php',             self::$paths['libraries']);

        /*
         *      We have a special loader adapter for Shyft which looks for "fallback" directories
         *      Fallback directories is searched in this order sites/site -> sites/all -> system
         */

        // Register the necessary Loader Adapters
        SLoader::registerAdapter(new SLoaderAdapterKoowa(self::$paths['libraries']));
        SLoader::registerAdapter(new SLoaderAdapterShyft(self::$paths['libraries']));
        SLoader::registerAdapter(new SLoaderAdapterComponent(self::$paths['components']));
        SLoader::registerAdapter(new SLoaderAdapterWidget(self::$paths['widgets']));
        SLoader::registerAdapter(new SLoaderAdapterAction(self::$paths['actions']));

        // Register the necessary Identifier Adapaters
        KIdentifier::registerAdapter(new SIdentifierAdapterKoowa());
        KIdentifier::registerAdapter(new SIdentifierAdapterShyft());
        KIdentifier::registerAdapter(new SIdentifierAdapterComponent());
        KIdentifier::registerAdapter(new SIdentifierAdapterWidget());
        KIdentifier::registerAdapter(new SIdentifierAdapterAction());
        KIdentifier::registerAdapter(new SIdentifierAdapterTheme());

        KIdentifier::registerApplication('site' , self::$paths['site']);

        define('FLOW', true);
    }

    /**
     * Get the version of the Shyft library
     */
    public static function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Get path to Shyft libraries
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
     *      TODO: Find a better location for findFile()
     *
     * @return string   Real path to the file
     */
    public function findFile($path, $directories)
    {
        // TODO: Implement caching here
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