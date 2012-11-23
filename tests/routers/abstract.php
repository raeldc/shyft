<?php

abstract class RouterTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		if(!defined('DOCUMENT_ROOT'))
			define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
		require_once DOCUMENT_ROOT.'/../../bootstrap.php';
	}
}