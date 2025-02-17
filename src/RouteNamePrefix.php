<?php
namespace EvolutionPHP\Routing;

class RouteNamePrefix
{
	private $prefix;
	public function __construct($namePrefix)
	{
		$this->prefix = rtrim($namePrefix,'.').'.';
	}

	public function group(callable $function)
	{
		Route::$_namePrefix = $this->prefix;
		call_user_func($function);
		Route::$_namePrefix = '';
	}
}