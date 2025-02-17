<?php
namespace EvolutionPHP\Routing;

class Collection
{
	private static $routes = [];
	public static function addRoute($routeName, $params){
		self::$routes[$routeName] = $params;
	}

	public static function setName($oldName, $newName)
	{
		self::$routes[$newName] = self::$routes[$oldName];
		unset(self::$routes[$oldName]);
	}

	public static function setDefaults($name, array $defaults)
	{
		self::$routes[$name]['defaults'] = $defaults;
	}

	public static function addRequirement($name, array $requirement)
	{
		self::$routes[$name]['requirements'] = array_merge(self::$routes[$name]['requirements'],$requirement);
	}

	public static function addMiddleware($name, array $middleware)
	{
		self::$routes[$name]['controller']['middleware'] = $middleware;
	}

	static function getRoutes()
	{
		return self::$routes;
	}
}