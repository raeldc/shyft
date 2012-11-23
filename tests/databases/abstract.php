<?php

abstract class DatabaseTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		if(!defined('DOCUMENT_ROOT'))
			define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
		require_once DOCUMENT_ROOT.'/../../bootstrap.php';

		KService::setConfig('shyft:database.adapter.mongo', array(
			'database' => 'shyft_tests',
			'options' => array(
				'host'     => 'localhost',
				'username' => '',
				'password' => '',
				'port'     => null,
			)
		));
	}
}