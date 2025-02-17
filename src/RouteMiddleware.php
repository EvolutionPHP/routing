<?php
namespace EvolutionPHP\Routing;

class RouteMiddleware
{
	private $middleware;
	public function __construct($middleware)
	{
		$this->middleware = is_array($middleware) ? $middleware : [$middleware];
	}

	public function group(callable $function)
	{
		Route::$_middleware = $this->middleware;
		call_user_func($function);
		Route::$_middleware = [];
	}
}