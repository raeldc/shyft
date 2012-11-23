<?php

class PagesTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		if(!defined('DOCUMENT_ROOT'))
			define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
		require_once DOCUMENT_ROOT.'/../../bootstrap.php';
	}

	public function testGetList()
	{
		$model = KService::get('com://site/pages.model.pages');

		$this->assertEquals($model->getTotal(), 2);

		// Next is to insert the data here in the script
		$data = array(
			array(
				'id' => '50af365918bf117615000000',
				'title' => 'Home',
				'slug' => 'home'
			),
			array(
				'id' => '50af538218bf11a315000001',
				'title' => 'About Me',
				'slug' => 'about'
			)
		);

		$list = $model->getList();
		$i = 0;

		foreach($list as $row){
			$this->assertEquals($row->toArray(), $data[$i]);
			$i++;
		}
	}
}