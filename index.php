<?php

define('FLOW', true);

/************************************************
 *  Define various constants for the system     *
 ************************************************/

define('SYSTEM_ROOT',       realpath(dirname(__FILE__)));
define('SYSTEM_LIBRARIES',  realpath(SYSTEM_ROOT.'/libraries'));
define('SYSTEM_COMPONENTS', realpath(SYSTEM_ROOT.'/components'));
define('SYSTEM_ACTIONS',    realpath(SYSTEM_ROOT.'/actions'));
define('SYSTEM_TEMPLATES',  realpath(SYSTEM_ROOT.'/templates'));
define('SYSTEM_LANGUAGES',  realpath(SYSTEM_ROOT.'/languages'));

/*********************************************
 *  Define various constants for all sites   *
 *********************************************/

define('SITES_ROOT',        SYSTEM_ROOT.'/sites/all');
define('SITES_LIBRARIES',   SITES_ROOT.'/libraries');
define('SITES_COMPONENTS',  SITES_ROOT.'/components');
define('SITES_ACTIONS',     SITES_ROOT.'/actions');
define('SITES_TEMPLATES',   SITES_ROOT.'/templates');
define('SITES_LANGUAGES',   SITES_ROOT.'/languages');

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
define('SITE_ACTIONS',      SITE_ROOT.'/actions');
define('SITE_TEMPLATES',    SITE_ROOT.'/templates');
define('SITE_LANGUAGES',    SITE_ROOT.'/languages');

/************************************************************************************************
 *                                   Initialize Flow                                            *
 ************************************************************************************************/

require_once SYSTEM_LIBRARIES.'/flow/flow.php';

Flow::getInstance(array(
    // Here, an array of paths are passed to the Flow instance. 
    // Flow will look for files in those folders, starting on the first path, until the file is found.
    'libraries'  => array(SITE_LIBRARIES,  SITES_LIBRARIES,    SYSTEM_LIBRARIES),
    'components' => array(SITE_COMPONENTS, SITES_COMPONENTS,   SYSTEM_COMPONENTS),
    'actions'    => array(SITE_ACTIONS,    SITES_ACTIONS,      SYSTEM_ACTIONS),
    'templates'  => array(SITE_TEMPLATES,  SITES_TEMPLATES,    SYSTEM_TEMPLATES),
    'languages'  => array(SITE_LANGUAGES,  SITES_LANGUAGES,    SYSTEM_LANGUAGES),
));

echo KFactory::get('com:application.dispatcher', array(
    //'behaviors' => KFactory::get('com:config')->get('application.behaviors')
))->dispatch();
