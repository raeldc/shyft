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
 * @author      Israel Canasa <shyft@me.com>
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
    protected $_path;

    /**
     * Already initialized?
     */
    protected $_initialized;

    /**
     * Container for paths to essential directories
     */
    protected $_paths = array();

    /**
     * Get the version of the Shyft library
     */
    public static function getVersion()
    {
        return self::VERSION;
    }

    public function initialize()
    {
    	if($this->_initialized) {
    		return $this;
    	}

        // Exception Classes
        require_once $this->findFile('koowa/exception/interface.php',      $this->_paths['libraries']);
        require_once $this->findFile('koowa/exception/exception.php',      $this->_paths['libraries']);

        // Loader Classes
        require_once $this->findFile('koowa/loader/adapter/interface.php', $this->_paths['libraries']);
        require_once $this->findFile('koowa/loader/adapter/exception.php', $this->_paths['libraries']);
        require_once $this->findFile('koowa/loader/adapter/abstract.php',  $this->_paths['libraries']);
        require_once $this->findFile('shyft/loader/adapter/koowa.php',     $this->_paths['libraries']);
        require_once $this->findFile('shyft/loader/adapter/shyft.php',     $this->_paths['libraries']);

        // Registry Classes
        require_once $this->findFile('koowa/loader/registry.php',          $this->_paths['libraries']);

        // Get Shyft Loader
        require_once $this->findFile('shyft/loader/loader.php',            $this->_paths['libraries']);

        // Inject the Koowa and Shyft loader adapters into the SLoader.
        $loader = SLoader::getInstance(array(
            'koowa_adapter' => new SLoaderAdapterKoowa(array('basepath' => $this->_paths['libraries'])),
            'shyft_adapter' => new SLoaderAdapterShyft(array('basepath' => $this->_paths['libraries']))
        ));

        // Register the Koowa and Shyft Identifier Adapters
        KServiceIdentifier::addLocator(new SServiceLocatorShyft(new KConfig()));

        //Setup the factory
        KService::getInstance()->set('shyft:loader', $loader);
        KService::getInstance()->set('koowa:loader', $loader);

        /*
         *      We have a special loader adapter for Shyft which looks for "fallback" directories
         *      Fallback directories is searched in this order sites/site -> sites/all -> system
         */

        // Register the application's Loader Adapters
        SLoader::addAdapter(new SLoaderAdapterComponent(array('basepath' => $this->_paths['components'])));

        // Register the application's Identifier Adapaters
        KServiceIdentifier::addLocator(KService::get('shyft:service.locator.component'));
        KServiceIdentifier::addLocator(KService::get('shyft:service.locator.theme'));

        KServiceIdentifier::setApplication('site' , $this->_paths['site']);

        $this->_initialized = true;

        return $this;
    }

    public function setPaths($paths)
    {
        $this->_paths['libraries']   = (isset($paths['libraries']))  ? $paths['libraries']   : SYSTEM_LIBRARIES;
        $this->_paths['components']  = (isset($paths['components'])) ? $paths['components']  : SYSTEM_COMPONENTS;
        $this->_paths['templates']   = (isset($paths['templates']))  ? $paths['templates']   : SYSTEM_TEMPLATES;
        $this->_paths['site']   = (isset($paths['site']))  ? $paths['site']   : DOCUMENT_ROOT;

    	return $this;
    }

    /**
     * Get path to Shyft libraries
     */
    public static function getPath()
    {
        if(!isset($this->_path)) {
            $this->_path = dirname(__FILE__);
        }

        return $this->_path;
    }

    public static function getInstance()
    {
    	static $instance;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
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

function text($string)
{
    return $string;
}