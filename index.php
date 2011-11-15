<?php
/************************************************
 *  Define various constants for the system     *
 ************************************************/

define('SYSTEM_ROOT',       realpath(dirname(__FILE__)));
define('SYSTEM_LIBRARIES',  realpath(SYSTEM_ROOT.'/libraries'));
define('SYSTEM_COMPONENTS', realpath(SYSTEM_ROOT.'/components'));
define('SYSTEM_THEMES',     realpath(SYSTEM_ROOT.'/themes'));

/*********************************************
 *  Define various constants for all sites   *
 *********************************************/

define('SITES_ROOT',        SYSTEM_ROOT.'/sites/all');
define('SITES_LIBRARIES',   SITES_ROOT.'/libraries');
define('SITES_COMPONENTS',  SITES_ROOT.'/components');
define('SITES_THEMES',      SITES_ROOT.'/themes');

/************************************************************************************************
 *                  Automatically detect the site resource that will be used.                   *
 *              The site application directory must contain a settings.php file.                *
 ************************************************************************************************/

$site = (is_dir($site='sites/'.$_SERVER['HTTP_HOST'])) ? $site: 'sites/default';

/************************************************************************************************
 *                        Define various constants for the site                                 *
 ************************************************************************************************/

define('SITE_ROOT',         realpath(SYSTEM_ROOT.'/'.$site));
define('SITE_LIBRARIES',    SITE_ROOT.'/libraries');
define('SITE_COMPONENTS',   SITE_ROOT.'/components');
define('SITE_THEMES',       SITE_ROOT.'/themes');

/************************************************************************************************
 *                               Initialize Shyft Subframework                                   *
 ************************************************************************************************/

define('DS', DIRECTORY_SEPARATOR);
require_once SYSTEM_LIBRARIES.'/shyft/shyft.php';

Shyft::getInstance(array(
    // Here, an array of paths are passed to the Shyft instance. 
    // Shyft will look for files in those folders, starting on the first path, until the file is found.
    'libraries'  => array(SITE_LIBRARIES,  SITES_LIBRARIES,    SYSTEM_LIBRARIES),
    'components' => array(SITE_COMPONENTS, SITES_COMPONENTS,   SYSTEM_COMPONENTS),
    'themes'     => array(SITE_THEMES,     SITES_THEMES,       SYSTEM_THEMES),
    'site'       => array(SITE_ROOT,       SITES_ROOT,         SYSTEM_ROOT),
));

echo KService::get('com://site/application.dispatcher', array(
    //'behaviors' => $this->getService('com:config')->get('application.behaviors')
))->dispatch();