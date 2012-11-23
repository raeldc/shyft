<?php
require 'abstract/router.php';

class ApplicationRouterTest extends RouterTest
{
	protected $router;

	public function setUp()
	{
		if(!defined('DOCUMENT_ROOT'))
			define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
		require_once DOCUMENT_ROOT.'/../bootstrap.php';

		$this->router = KService::get('shyft:router.default', array(
			'routes' => array(
				'[<lang>/]admin[/<com>[/<uri>][.<format>]]'        => 'route=admin-component&mode=admin&com=dashboard',
				'[<lang>/]admin/pages[/<page>][/<uri>][.<format>]' => 'route=admin-pages&mode=admin&com=pages&page=',
				'[<lang>/][<page>[/<uri>][.<format>]]'             => 'route=site-pages&mode=site&com=content&page=home',
				'[<lang>/][<uri>[.<format>]]'                      => 'route=site-default&mode=site&com=content&page=home',
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
				'uri'    => '',
				'com'    => 'dashboard',
			),
		));
	}

	/**
     * @expectedException SRouterException
     */
    public function testCantFindMatch()
    {
    	// Should throw SRouterException because of unexpected characters
    	$this->router->parse('ph/admin:=/dashboard');
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
			$this->router->parse('ch/admin/pages/blog/special-+:_/character.json'),
			array(
				'lang'   => 'ch',
				'mode'   => 'admin',
				'com'    => 'pages',
				'page'   => 'blog',
				'uri'    => 'special-+:_/character',
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
		$this->assertEquals('en-gb/admin/pages/blog/edit/40.json', $this->router->build('route=admin-pages&mode=admin&lang=en-gb&com=pages&page=blog&uri=edit/40&format=json'));
		$this->assertEquals('admin/widgets/new', $this->router->build('route=admin-component&mode=admin&com=widgets&uri=new'));
		$this->assertEquals('en-gb/admin', $this->router->build('route=admin-component&mode=admin&lang=en-gb'));

		// tests if value is a default, don't build it into the route. In this case, lang=en is the default lang.
		$this->assertEquals('admin/widgets/new', $this->router->build('route=admin-component&mode=admin&com=widgets&uri=new&lang=en'));
	}
}