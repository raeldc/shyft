<?php

/* 
 * Routers build pretty urls from a URL Query, they can also parse pretty URLs into a query array 
 *		First, create rules on how a URL is parsed or built.
*/
abstract class SRouter extends KObject
{	
	/**
     * List of route objects
     *
     * @var array
     */
	protected static $_routes;
	
	public function __construct(KConfig $config)
	{
		$this->_routes = new ArrayObject();
	}

	public static function create($name = 'default')
	{
		return $this;
	}

	public static function build($query, $name = 'default')
	{
		
	}

	public function parse($route)
	{

	}

	public function getRoute($name = 'default')
	{
		
	}
}