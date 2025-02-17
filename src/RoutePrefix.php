<?php
namespace EvolutionPHP\Routing;

class RoutePrefix
{
	private $prefix;
	public function __construct($prefix)
	{
		$this->prefix = '/'.ltrim($prefix, '/');
	}
	public function group(callable $function)
	{
		Route::$_prefix = $this->prefix;
		call_user_func($function);
		Route::$_prefix = '';
	}
}