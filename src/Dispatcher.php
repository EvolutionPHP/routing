<?php
namespace EvolutionPHP\Routing;

use EvolutionPHP\HTTP\HTTP;
use EvolutionPHP\HTTP\Request;
use EvolutionPHP\Instance\Instance;
use EvolutionPHP\Routing\Exceptions\NotAllowedException;
use EvolutionPHP\Routing\Exceptions\NotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Dispatcher
{
	private $siteRoutes;
	public function __construct(array $routes)
	{
		$this->siteRoutes = $routes;
	}
	public function dispatch()
	{
		if(!$params = $this->initRoutes()){
			throw new \Exception("Route not found");
		}
		if(count($params['_controller']['middleware']) > 0){
			foreach ($params['_controller']['middleware'] as $middleware) {
				if(!$this->initMiddleware($middleware)){
					throw new \Exception('Invalid resource.');
				}
			}
		}
		if($params['_controller']['type'] == 'function'){
			$this->callFunction($params);
		}else{
			$this->callClass($params);
		}
	}

	private function initMiddleware($middleware)
	{
		if(is_callable($middleware)){
			die('ok');
		}elseif (class_exists($middleware)) {
			$class = new $middleware();
			if(!method_exists($class, 'handle')){
				throw new \Exception('Method handle() does not exist for middleware '.$middleware);
			}else{
				$result = $class->handle(HTTP::request(), function (Request $request){
					return $request;
				});
				return Middleware::response($result);
			}
		}
	}

	private function initRoutes()
	{
		$routeCollection = new RouteCollection();
		foreach ($this->siteRoutes as $name => $route){
			$routeClass = new \Symfony\Component\Routing\Route($route['path'], [
				'_controller' => is_array($route['controller']) ? $route['controller'] : [$route['controller']],
			]);
			$routeClass->setRequirements($route['requirements']);
			$routeClass->setMethods($route['method']);
			if(count($route['defaults']) > 0){
				$routeClass->addDefaults(['_defaults' => $route['defaults']]);
			}
			$routeCollection->add($name, $routeClass);
		}

		try {
			$context = new RequestContext();
			$context->fromRequest(HTTP::request()->http());
			$matcher = new UrlMatcher($routeCollection, $context);
			$params = $matcher->match($context->getPathInfo()) ;
			$utils = Instance::register(Utils::class, [$routeCollection, $context]);
			$utils->setParams($params);
			$utils->setRoutes($this->siteRoutes);
			return $params;
		}catch (ResourceNotFoundException $exception){
			throw new NotFoundException('Route not found');
		}catch (MethodNotAllowedException $exception){
			throw new NotAllowedException('Method not allowed');
		}catch (\Exception $exception){
			throw new \Exception($exception->getMessage());
		}
	}


	private function callFunction($params)
	{
		$controller = $params['_controller'];
		if(!is_callable($controller['handler'])){
			throw new \Exception('Handler is not callable in route '.$params['_route']);
		}
		$functionArguments = $this->getControllerArguments($params);

		$ref = new \ReflectionFunction($controller['handler']);
		$functionParameters = $ref->getParameters();
		$args = [];
		if(count($functionParameters) > 0){
			foreach ($functionParameters as $parameter){
				if(array_key_exists($parameter->getName(), $functionArguments)){
					$args[] = $functionArguments[$parameter->getName()];
				}

			}
		}
		$result = $controller['handler'](...$args);
		HTTP::response()->send($result);
	}

	private function callClass($params)
	{
		$controller = $params['_controller'];
		if(!class_exists($controller['handler'])){
			throw new \Exception($controller['handler'].' class not exists in route '.$params['_route']);
		}
		$class = new $controller['handler'];
		if(!method_exists($class, $controller['method'])){
			throw new \Exception($controller['method'].' method not exists in class '.$controller['handler']);
		}
		$classArguments = array_values($this->getControllerArguments($params));
		$result = $class->{$controller['method']}(...$classArguments);
		HTTP::response()->send($result);
	}

	private function getControllerArguments($params)
	{
		unset($params['_controller'], $params['_route']);
		return $params;
	}
}