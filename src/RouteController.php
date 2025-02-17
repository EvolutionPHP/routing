<?php
namespace EvolutionPHP\Routing;

class RouteController
{
	private $controller;
	public function __construct($controller)
	{
		$this->controller = $controller;
	}

	public function group(callable $function)
	{
		Route::$_controller = $this->controller;
		call_user_func($function);
		Route::$_controller = '';
	}
}