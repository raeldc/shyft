<?php
/**
 * @category	Shyft
 * @package		Shyft_Identifier
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Identifier Adapter for a component
 *
 * @author		Israel Canasa <shyft@me.com>
 * @category	Shyft
 * @package     Shyft_Identifier
 * @subpackage 	Adapter
 */
class SServiceLocatorComponent extends KServiceLocatorAbstract
{
	/** 
	 * The adapter type
	 * 
	 * @var string
	 */
	protected $_type = 'com';

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
         $config->append(array(
            'loader'  => $this->getService('shyft:loader'),
        ));
        
        parent::_initialize($config);
    }
	
	/**
	 * Get the classname based on an identifier
	 * 
	 * This factory will try to create an generic or default classname on the identifier information
	 * if the actual class cannot be found using a predefined fallback sequence.
	 * 
	 * Fallback sequence : -> Named Component Specific
	 *                     -> Named Component Default  
	 *                     -> Default Component Specific
	 *                     -> Default Component Default
	 *                     -> Framework Specific 
	 *                     -> Framework Default
	 *
	 * @param mixed  		 Identifier or Identifier object - com:[//application/]component.view.[.path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{ 
	    $path      = KInflector::camelize(implode('_', $identifier->path));
        $classname = 'Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
        
      	//Manually load the class to set the basepath
		if (!$this->getService('shyft:loader')->loadClass($classname, $identifier->basepath))
		{
		    $classpath = $identifier->path;
			$classtype = !empty($classpath) ? array_shift($classpath) : '';
					
			//Create the fallback path and make an exception for views
			$path = ($classtype != 'view') ? KInflector::camelize(implode('_', $classpath)) : '';
						
			/*
			 * Find the classname to fallback too and auto-load the class
			 * 
			 * Fallback sequence : -> Named Component Specific
			 *                     -> Named Component Default
			 *                     -> Default Component Specific
			 *                     -> Default Component Default
			 *                     -> Shyft Component Specific
			 *                     -> Shyft Component Default
			 *                     -> Framework Specific
			 *                     -> Framework Default
			 */

			$classnames = array(
				'Com'.ucfirst($identifier->package).ucfirst($classtype).$path.ucfirst($identifier->name),
				'Com'.ucfirst($identifier->package).ucfirst($classtype).$path.'Default',
				'ComDefault'.ucfirst($classtype).$path.ucfirst($identifier->name),
				'ComDefault'.ucfirst($classtype).$path.'Default',
				'S'.ucfirst($classtype).$path.ucfirst($identifier->name),
				'S'.ucfirst($classtype).$path.ucfirst($identifier->name).'Default',
				'S'.ucfirst($classtype).$path.'Default',
				'K'.ucfirst($classtype).$path.ucfirst($identifier->name),
				'K'.ucfirst($classtype).$path.ucfirst($identifier->name).'Default',
				'K'.ucfirst($classtype).$path.'Default'
			);

			foreach ($classnames as $classname) 
			{
				if(class_exists($classname)) return $classname;
			}

			$classname = false;
		}

		return $classname;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	An Identifier object - com:[//application/]component.view.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KServiceIdentifier $identifier)
	{
        $path  = '';
	    $parts = $identifier->path;
				
		$component = strtolower($identifier->package);
			
		if(!empty($identifier->name))
		{
			if(count($parts)) 
			{
				$path    = KInflector::pluralize(array_shift($parts));
				$path   .= count($parts) ? '/'.implode('/', $parts) : '';
				$path   .= '/'.strtolower($identifier->name);	
			} 
			else $path  = strtolower($identifier->name);	
		}

		$path = Shyft::getInstance()->findFile('/components/'.$component.'/'.$path.'.php', $identifier->basepath);

		return $path;
	}
}