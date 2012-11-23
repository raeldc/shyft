<?php
require 'abstract.php';

class BaseRouterTest extends RouterTest
{
	protected $router;

	public function setUp()
	{
		parent::setUp();

		$this->router = KService::get('shyft:router.default', array(
			'routes' => array(
				'new'                 => 'route=new&view=content&layout=form',
				'edit/<id>'           => 'route=edit&view=content&layout=form',
				'contents[/<layout>]' => 'route=list&view=contents',
				'<id>'      		  => 'route=default&view=content',
			),
			'defaults' => array(
				'view'   => 'content',
				'layout' => 'default'
			)
		));
	}

	public function testBaseRouterParsing()
	{
		$this->assertEquals(
			$this->router->parse('50'),
			array(
				'view'   => 'content',
				'layout' => 'default',
				'id'	 => '50',
			)
		);

		$this->assertEquals(
			$this->router->parse('slug-of-item'),
			array(
				'view'   => 'content',
				'layout' => 'default',
				'id'	 => 'slug-of-item',
			)
		);


		$this->assertEquals(
			$this->router->parse('edit/50'),
			array(
				'view'   => 'content',
				'layout' => 'form',
				'id'     => '50'
			)
		);

		$this->assertEquals(
			$this->router->parse('new'),
			array(
				'view'   => 'content',
				'layout' => 'form',
			)
		);

		$this->assertEquals(
			$this->router->parse('contents'),
			array(
				'view'   => 'contents',
				'layout' => 'default',
			)
		);

		$this->assertEquals(
			$this->router->parse('contents/list'),
			array(
				'view'   => 'contents',
				'layout' => 'list',
			)
		);
	}

	public function testBaseRouterBuilding()
	{
		$this->assertEquals('edit/50', $this->router->build('route=edit&id=50'));
		$this->assertEquals('new', $this->router->build('route=new'));
		$this->assertEquals('contents/list', $this->router->build('route=list&layout=list'));
		$this->assertEquals('contents', $this->router->build('route=list'));
		$this->assertEquals('slug-of-item', $this->router->build('route=default&id=slug-of-item'));
	}
}