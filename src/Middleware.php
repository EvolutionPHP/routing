<?php
namespace EvolutionPHP\Routing;

class Middleware
{
	static function response(\EvolutionPHP\HTTP\Request $request)
	{
		return true;
	}
}