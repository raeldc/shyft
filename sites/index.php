<?php

define('DOCUMENT_ROOT',       realpath(dirname(__FILE__)));

// It's OK to change this. The location of bootstrap determines the relative path of necessary directories
require_once '../bootstrap.php';

/*
echo KService::get('com://site/application.dispatcher', array(
    //'behaviors' => $this->getService('com:config')->get('application.behaviors')
))->dispatch();
*/