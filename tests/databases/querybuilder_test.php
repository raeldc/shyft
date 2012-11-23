<?php
require_once 'abstract.php';

class QueryBuilderTest extends DatabaseTest
{
	public $query;

	public function setUp()
	{
		parent::setUp();

		$this->query = KService::get('shyft:database.query.mongo', array(
			'schema' => KService::get('shyft:database.schema.tests', array(
				'dynamic' => true,
				'fields' => array(
					'id' => array(
						'type' => 'mongoid'
					),
					'name' => array(
						'type' => 'string'
					)
				)
			))
		));
	}

	public function testSelect()
	{
		$this->query->from('shyft_tests')->field('id')->equalTo('something');
		var_dump($this->query->compile('where'));
	}
}