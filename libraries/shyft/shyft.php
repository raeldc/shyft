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

        // Exception Classes
        require_once self::findFile('koowa/exception/interface.php',      self::$paths['libraries']);
        require_once self::findFile('koowa/exception/exception.php',      self::$paths['libraries']);

        // Loader Classes
        require_once self::findFile('koowa/loader/adapter/interface.php', self::$paths['libraries']);
        require_once self::findFile('koowa/loader/adapter/exception.php', self::$paths['libraries']);
        require_once self::findFile('koowa/loader/adapter/abstract.php',  self::$paths['libraries']);
        require_once self::findFile('shyft/loader/adapter/koowa.php',     self::$paths['libraries']);
        require_once self::findFile('shyft/loader/adapter/shyft.php',     self::$paths['libraries']);

        // Registry Classes
        require_once self::findFile('koowa/loader/registry.php',          self::$paths['libraries']);

        // Get Shyft Loader
        require_once self::findFile('shyft/loader/loader.php',            self::$paths['libraries']);
        
        // Inject the Koowa and Shyft loader adapters into the SLoader.
        $loader = SLoader::getInstance(array(
            'koowa_adapter' => new SLoaderAdapterKoowa(array('basepath' => self::$paths['libraries'])),
            'shyft_adapter' => new SLoaderAdapterShyft(array('basepath' => self::$paths['libraries']))
        ));

        // Register the Koowa and Shyft Identifier Adapters
        KServiceIdentifier::addLocator(new SServiceLocatorKoowa());
        KServiceIdentifier::addLocator(new SServiceLocatorShyft());

        //Setup the factory
        KService::getInstance()->set('shyft:loader', $loader);
        KService::getInstance()->set('koowa:loader', $loader);

        /*
         *      We have a special loader adapter for Shyft which looks for "fallback" directories
         *      Fallback directories is searched in this order sites/site -> sites/all -> system
         */

        // Register the application's Loader Adapters
        SLoader::addAdapter(new SLoaderAdapterComponent(array('basepath' => self::$paths['components'])));
        SLoader::addAdapter(new SLoaderAdapterWidget(array('basepath'    => self::$paths['widgets'])));
        SLoader::addAdapter(new SLoaderAdapterAction(array('basepath'    => self::$paths['actions'])));

        // Register the application's Identifier Adapaters
        KServiceIdentifier::addLocator(KService::get('shyft:service.locator.component'));
        KServiceIdentifier::addLocator(KService::get('shyft:service.locator.widget'));
        KServiceIdentifier::addLocator(KService::get('shyft:service.locator.action'));
        KServiceIdentifier::addLocator(KService::get('shyft:service.locator.theme'));

        KServiceIdentifier::setApplication('site' , self::$paths['site']);

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

function debug($value)
{
    require_once 'ChromePhp.php';
    ChromePhp::useFile(SYSTEM_ROOT.'/logs', KRequest::base().'/logs');
    call_user_func_array(array('ChromePhp', 'log'), func_get_args());
}