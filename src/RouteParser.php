<?php
namespace EvolutionPHP\Routing;

class RouteParser
{
	private $routeName = '';
	private $path_args = [];
	private $namePrefix = '';
	private $middleware = [];
	/**
	 * @param string|array $method GET, POST, PUT, DELETE, PATCH, OPTIONS ALL
	 * @param string $path
	 * @param array|callable $handler
	 * @return $this
	 */
	public function addRoute($method, $path, $handler, $namePrefix='', $middleware=[])
	{
		$this->namePrefix = $namePrefix;
		$this->middleware = $middleware;
		$method = $this->parseMethod($method); //Return an array with the
		$pathParser = $this->parsePath($path); //Return an array with path and requirements (optional)
		$urlPath = $pathParser['path'];
		$this->routeName = $urlPath;
		$this->path_args = $pathParser['requirements'];
		if(!is_array($handler)){
			//Handler is a function
			$controller = [
				'type' => 'function',
				'handler' => $handler,
				'args' => [],
				'middleware' => $this->middleware
			];
		}else{
			//Handler is a class
			$controller = [
				'type' => 'class',
				'handler' => $handler[0],
				'method' => $handler[1],
				'args' => [],
				'middleware' => $this->middleware
			];
		}

		Collection::addRoute($this->routeName, [
			'path' => $this->routeName,
			'requirements' => [],
			'defaults' => [],
			'controller' => $controller,
			'method' => $method
		]);
		return $this;
	}

	/**
	 * @param $method
	 * @return array
	 */
	private function parseMethod($method)
	{
		if(is_array($method)) {
			$method = array_map(function ($m){
				return strtoupper($m);
			}, $method);
		}else{
			$method =strtoupper($method);
			if($method == 'ALL'){
				$method = ['GET','POST','PUT','DELETE'];
			}else{
				$method = [$method];
			}
		}
		return $method;
	}

	/**
	 * @param string $path
	 * @return array
	 */
	private function parsePath($path)
	{
		if(preg_match_all('/{([a-zA-Z]+)}/', $path, $output_array)){
			$requirements = [];
			foreach ($output_array[0] as $key => $value) {
				$path = str_replace($value, '{'.$output_array[1][$key].'}', $path);
				$requirements[] = $output_array[1][$key];
			}
			return [
				'path' => $path,
				'requirements' => $requirements
			];
		}else{
			return [
				'path' => $path,
				'requirements' => []
			];
		}
	}

	/*
	 * ==============================================
	 * Queries
	 * ==============================================
	 */
	public function name($routeName)
	{
		Collection::setName($this->routeName, $this->namePrefix.$routeName);
		$this->routeName = $routeName;
		return $this;
	}
	public function default(array $defaults)
	{
		Collection::setDefaults($this->routeName, $defaults);
	}


	public function where($parameter, $regularExpression=null)
	{
		if(is_array($parameter)){
			foreach ($parameter as $key => $value) {
				$this->where($key, $value);
			}
		}else{
			if(in_array($parameter, $this->path_args)){
				Collection::addRequirement($this->routeName, [$parameter => $regularExpression]);
			}
		}
		return $this;
	}


	public function whereNumber($parameter)
	{
		if(is_array($parameter)){
			foreach ($parameter as $key => $value) {
				$this->whereNumber($key);
			}
		}else{
			$this->where($parameter, '[0-9]+');
		}
		return $this;
	}

	public function whereAlpha($parameter)
	{
		if(is_array($parameter)){
			foreach ($parameter as $key => $value) {
				$this->whereAlpha($key);
			}
		}else{
			$this->where($parameter, '[A-Za-z]+');
		}
		return $this;
	}

	public function whereAlphaNumeric($parameter)
	{
		if(is_array($parameter)){
			foreach ($parameter as $key => $value) {
				$this->whereAlphaNumeric($key);
			}
		}else{
			$this->where($parameter, '[A-Za-z0-9]+');
		}
		return $this;
	}

	public function whereIn($parameter, array $list)
	{
		$this->where($parameter, implode('|', $list));
	}

	public function withMiddleware(string|array|callable $middleware)
	{
		if(is_array($middleware)){
			$this->middleware = array_merge($this->middleware, $middleware);
		}else{
			$this->middleware[] = $middleware;
		}
		Collection::addMiddleware($this->routeName, $this->middleware);
		return $this;
	}
}