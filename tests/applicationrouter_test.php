<?php

class ApplicationRouterTest extends PHPUnit_Framework_TestCase
{
	protected $router;

	public function setUp()
	{
		if(!defined('DOCUMENT_ROOT'))
			define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
		require_once DOCUMENT_ROOT.'/../bootstrap.php';

		$this->router = KService::get('shyft:router.default', array(
			'routes' => array(
				'[<lang>/]admin[/<com>[/<uri>][.<format>]]'        => 'mode=admin&com=dashboard',
				'[<lang>/]admin/pages[/<page>][/<uri>][.<format>]' => 'mode=admin&com=pages',
				'[<lang>/][<page>[/<uri>][.<format>]]'           => 'mode=site&com=content',
				'[<lang>/][<uri>[.<format>]]'                    => 'mode=site&com=content',
			),
			'regex' => array(
				'lang'	 => '^[a-z]{2,2}|^[a-z]{2,2}-[a-z]{2,2}',
				'uri'    => '[a-zA-Z0-9\-+:_/]+',
				'format' => '[a-z]+$',
				// @TODO: must be populated by all installed components.
				'com'	 => array('dashboard','widgets'),
				// @TODO: must be populated by all enabled pages
				'page'   => 'home|blog|about-us|users',
			),
			'defaults' => array(
				'mode'   => 'site',
				'lang'   => 'en',
				'format' => 'html',
				'page'   => 'home',
				'uri'    => '',
				'com'    => 'dashboard',
			),
			'sefurl' => true,
		));
	}

	public function testApplicationRouterParsing()
	{
		$this->assertTrue($this->arraysAreSimilar(
			$this->router->parse('en-gb/admin/widgets/edit/40.html'),
			array(
				'lang'   => 'en-gb',
				'mode'   => 'admin',
				'com'    => 'widgets',
				'uri'    => 'edit/40',
				'format' => 'html',
				'page'   => 'home'
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$this->router->parse('ch/admin/pages/blog/edit/50-50.json'),
			array(
				'lang'   => 'ch',
				'mode'   => 'admin',
				'com'    => 'pages',
				'page'   => 'blog',
				'uri'    => 'edit/50-50',
				'format' => 'json',
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$this->router->parse('en/blog/edit/50-50.json'),
			array(
				'lang'   => 'en',
				'mode'   => 'site',
				'page'   => 'blog',
				'uri'    => 'edit/50-50',
				'format' => 'json',
				'com'    => 'content',
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$this->router->parse('about-us'),
			array(
				'lang'   => 'en',
				'mode'   => 'site',
				'page'   => 'about-us',
				'uri'    => '',
				'format' => 'html',
				'com'    => 'content',
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$this->router->parse('users/list'),
			array(
				'lang'   => 'en',
				'mode'   => 'site',
				'page'   => 'users',
				'uri'    => 'list',
				'format' => 'html',
				'com'    => 'content',
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$this->router->parse('title-of-content.json'),
			array(
				'lang'   => 'en',
				'mode'   => 'site',
				'page'   => 'home',
				'uri'    => 'title-of-content',
				'format' => 'json',
				'com'    => 'content',
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$this->router->parse('en-gb/title-of-content.json'),
			array(
				'lang'   => 'en-gb',
				'mode'   => 'site',
				'page'   => 'home',
				'uri'    => 'title-of-content',
				'format' => 'json',
				'com'    => 'content',
			)
		));

		$this->assertTrue($this->arraysAreSimilar(
			$this->router->parse(''),
			array(
				'lang'   => 'en',
				'mode'   => 'site',
				'page'   => 'home',
				'uri'    => '',
				'format' => 'html',
				'com'    => 'content',
			)
		));
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