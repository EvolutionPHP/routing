<?php
namespace EvolutionPHP\Routing;

use EvolutionPHP\HTTP\HTTP;
use EvolutionPHP\Instance\Instance;

class Routing
{
	static private $utils;

	static private function getUtils()
	{
		if(!self::$utils) {
			self::$utils = Instance::get(Utils::class);
		}
		return self::$utils;
	}
	static function generateURL($name, $parameters=[])
	{
		return self::getUtils()->getURL($name, $parameters);
	}

	static function routeName()
	{
		return self::getUtils()->getParams()['_route'];
	}

	static function redirect($url, $status = 302)
	{
		if(filter_var($url, FILTER_VALIDATE_URL)) {
			HTTP::response()->redirect($url, $status);
		}else{
			HTTP::response()->redirect(self::getUtils()->baseURL().$url, $status);
		}
	}

	static function routeList()
	{
		return self::getUtils()->getRoutes();
	}
}