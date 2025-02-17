<?php
namespace EvolutionPHP\Routing;
class Route
{
	public static $_controller = '';
	public static $_prefix = '';
	public static $_namePrefix = '';
	public static $_middleware = [];
	/**
	 * @param $path
	 * @param array|callable $callback
	 * @return RouteParser
	 */
	static function get(string $path, string|array|callable $callback)
	{
		return self::addRoute('GET', $path, $callback);
	}

	static function post(string $path, string|array|callable $callback)
	{
		return self::addRoute('POST', $path, $callback);
	}
	static function delete(string $path, string|array|callable $callback)
	{
		return self::addRoute('DELETE', $path, $callback);
	}

	static function put(string $path, string|array|callable $callback)
	{
		return self::addRoute('PUT', $path, $callback);
	}

	static function patch(string $path, string|array|callable $callback)
	{
		return self::addRoute('PATCH', $path, $callback);
	}

	static function options(string $path, string|array|callable $callback)
	{
		return self::addRoute('OPTIONS', $path, $callback);
	}

	static function any(string $path, string|array|callable $callback)
	{
		return self::addRoute('ALL', $path, $callback);
	}

	static function match(array $method, string $path, string|array|callable $callback)
	{
		return self::addRoute($method, $path, $callback);
	}

	static function redirect($path, $pathToRedirect, $statusCode = 302)
	{
		self::addRoute('ALL', $path, function () use ($pathToRedirect, $statusCode){
			Routing::redirect($pathToRedirect, $statusCode);
		});
	}

	static function permanentRedirect($path, $pathToRedirect)
	{
		self::redirect($path, $pathToRedirect, 301);
	}

	static function controller($controller)
	{
		return new RouteController($controller);
	}

	static function prefix($routePrefix)
	{
		return new RoutePrefix($routePrefix);
	}

	static function namePrefix($namePrefix)
	{
		return new RouteNamePrefix($namePrefix);
	}

	static function middleware(string|array|callable $middleware)
	{
		return new RouteMiddleware($middleware);
	}

	static private function addRoute($method, $path, $callback)
	{
		$routeClass = new RouteParser();
		if(self::$_controller != '' && is_string($callback)){
			$callback = [self::$_controller, $callback];
		}
		$path = self::$_prefix.$path;
		return $routeClass->addRoute($method, $path, $callback, self::$_namePrefix, self::$_middleware);
	}

	static function dispatch()
	{
		$dispatch = new Dispatcher(Collection::getRoutes());
		$dispatch->dispatch();
	}
}