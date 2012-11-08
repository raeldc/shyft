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
				'[<lang>][/][<page>[/<uri>][.<format>]]'           => 'mode=site&page=&uri=',
				'[<lang>][/][<uri>[.<format>]]'                    => 'mode=site&page=&uri=',
			),
			'regex' => array(
				'lang'	 => '^[a-z]{2,2}|^[a-z]{2,2}-[a-z]{2,2}',
				'uri'    => '[a-zA-Z0-9\-+.:_/]+',
				'format' => '[a-z]+$',
				// @TODO: must be populated by all installed components.
				'com'	 => array('dashboard','widgets'),
				// @TODO: must be populated by all enabled pages
				'page'   => 'blog|about-us|contacts',
			),
			'defaults' => array(
				'mode'   => 'site',
				'lang'   => 'en',
				'format' => 'html',
				'page'   => '',
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
				'uri'    => 'edit/40.html',
				'format' => 'html',
				'page'   => ''
			)
		));
		var_dump($this->router->parse('ch/admin/pages/blog/edit/40.json'));
		$this->assertTrue($this->arraysAreSimilar(
			$this->router->parse('ch/admin/pages/blog/edit/40.json'),
			array(
				'lang'   => 'ch',
				'mode'   => 'admin',
				'com'    => 'pages',
				'page'   => 'blog',
				'uri'    => 'edit/40',
				'format' => 'json',
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