<?php
namespace EvolutionPHP\Routing;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Utils
{
	/**
	 * @var UrlGenerator
	 */
	private $urlGenerator;
	private $routeCollection;
	private $requestContext;
	private $params;
	private $routes;

	public function __construct(RouteCollection $routeCollection, RequestContext $requestContext)
	{
		$this->routeCollection = $routeCollection;
		$this->requestContext = $requestContext;
	}

	public function setParams($params)
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function setRoutes($routes)
	{
		$this->routes = $routes;
	}
	public function getRoutes(){
		return $this->routes;
	}

	public function getURL($name, $parameters)
	{
		if(!$this->urlGenerator){
			$this->urlGenerator = new UrlGenerator($this->routeCollection, $this->requestContext);
		}
		return $this->urlGenerator->generate($name, $parameters);
	}

	public function baseURL()
	{
		return $this->requestContext->getBaseUrl();
	}
}