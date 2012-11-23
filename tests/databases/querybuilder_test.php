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

	/**
	 * @see http://php.net/manual/en/mongo.sqltomongo.php
	 */
	public function testBasicWhere()
	{
		$this->query->reset();

		// SELECT * FROM users WHERE age=33
		$this->query
			->field('age')
			->equalTo('33');

		$this->assertEquals(array(
				'$and' => array(
					array('age' => '33')
				)
			),
			$this->query->compile('where')
		);

		$this->query->reset();

		// SELECT * FROM users WHERE age>33
		$this->query
			->field('age')
			->gt('33');

		$this->assertEquals(array(
				'$and' => array(
					array('age' =>
						// string 33 or int shouldn't matter
						array('$gt' => 33)
					)
				)
			),
			$this->query->compile('where')
		);

		$this->query->reset();

		// SELECT * FROM users WHERE age<33
		$this->query
			->field('age')
			->lt('33');

		$this->assertEquals(array(
				'$and' => array(
					array('age' =>
						// string 33 or int shouldn't matter
						array('$lt' => 33)
					)
				)
			),
			$this->query->compile('where')
		);

		$this->query->reset();

		// SELECT * FROM users WHERE name LIKE "%Joe%"
		$this->query
			->field('name')
			->regex('/Joe/');

		$this->assertEquals(array(
				'$and' => array(
					array('name' =>
						new MongoRegex('/Joe/')
					)
				)
			),
			$this->query->compile('where')
		);

		$this->query->reset();

		// SELECT * FROM users WHERE name LIKE "Joe%"
		$this->query
			->field('name')
			->regex('/^Joe/');

		$this->assertEquals(array(
				'$and' => array(
					array('name' =>
						new MongoRegex('/^Joe/')
					)
				)
			),
			$this->query->compile('where')
		);

		$this->query->reset();

		// SELECT * FROM users WHERE age>33 AND age<=40

		$this->query
			->field('age')
			->gt('33')
			->field('age')
			->lte(40);

		// There is yet no algorithm to optimize how queries are generated so comparisons for a field can be like this array("age" => array('$gt' => 33, '$lte' => 40))
		$this->assertEquals(array(
				'$and' => array(
					array(
						'age' => array('$gt' => 33),
					),
					array(
						'age' => array('$lte' => 40)
					)
				)
			),
			$this->query->compile('where')
		);

		$this->query->reset();

		// SELECT * FROM users WHERE a=1 or b=2
		$this->query
			->openOr
				->field('a')
				->equalTo('1')
				->field('b')
				->equalTo(2)
			->close;

		$this->assertEquals(array(
				'$and' => array(
					array(
						'$or' => array(
							array('a' => 1),
							array('b' => 2),
						)
					)
				)
			),
			$this->query->compile('where')
		);
	}
}