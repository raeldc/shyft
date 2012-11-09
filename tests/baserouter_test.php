<?php

class BaseRouterTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		if(!defined('DOCUMENT_ROOT'))
			define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
		require_once DOCUMENT_ROOT.'/../bootstrap.php';
	}

	public function testBaseRouterParsing()
	{
		$router = KService::get('shyft:router.default', array(
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

		$this->assertTrue($this->arraysAreSimilar(
			$router->parse('50'),
			array(
				'view'   => 'content',
				'layout' => 'default',
				'id'	 => '50',
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$router->parse('slug-of-item'),
			array(
				'view'   => 'content',
				'layout' => 'default',
				'id'	 => 'slug-of-item',
			)
		));


		$this->assertTrue($this->arraysAreSimilar(
			$router->parse('edit/50'),
			array(
				'view'   => 'content',
				'layout' => 'form',
				'id'     => '50'
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$router->parse('new'),
			array(
				'view'   => 'content',
				'layout' => 'form',
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$router->parse('contents'),
			array(
				'view'   => 'contents',
				'layout' => 'default',
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$router->parse('contents/list'),
			array(
				'view'   => 'contents',
				'layout' => 'list',
			)
		));
	}

	public function testApplicationRouterParsing()
	{

	}

	public function testBaseRouterBuilding()
	{

	}

	public function testApplicationRouterBuilding()
	{

	}

	public function arraysAreSimilar($a, $b)
	{
		// if the indexes don't match, return immediately
		if (count(array_diff_assoc($a, $b))) {
			return false;
		}
		// we know that the indexes, but maybe not values, match.
		// compare the values between the two arrays
		foreach($a as $k => $v)
		{
			if ($v !== $b[$k]) {
				return false;
			}
		}
		// we have identical indexes, and no unequal values
		return true;
	}
}