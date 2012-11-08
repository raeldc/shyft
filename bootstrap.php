<?php
/************************************************
 *  Define various constants for the system     *
 ************************************************/

define('SYSTEM_ROOT',       realpath(dirname(__FILE__)));
define('SYSTEM_LIBRARIES',  realpath(SYSTEM_ROOT.'/libraries'));
define('SYSTEM_COMPONENTS', realpath(SYSTEM_ROOT.'/components'));
define('SYSTEM_TEMPLATES',  realpath(SYSTEM_ROOT.'/templates'));

/****************************************************************************************
 *                               Initialize Framework                                   *
 ****************************************************************************************/

require_once SYSTEM_LIBRARIES.'/autoload.php';
require_once SYSTEM_LIBRARIES.'/shyft/shyft.php';

Shyft::getInstance()->setPaths([
	'libraries'  => SYSTEM_LIBRARIES,
	'components' => SYSTEM_COMPONENTS,
	'templates'  => SYSTEM_TEMPLATES,
	'site' 		 => DOCUMENT_ROOT
])->initialize();