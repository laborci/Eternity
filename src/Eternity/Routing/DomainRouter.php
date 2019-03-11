<?php namespace Eternity\Routing;

use Eternity\Application\WebApp;
use Eternity\ServiceManager\Service;
use Eternity\ServiceManager\ServiceContainer;
use Eternity\ServiceManager\SharedService;
use Symfony\Component\HttpFoundation\Request;

class DomainRouter implements SharedService {

	use Service;

	protected $request;
	protected $domain = '';

	public function __construct(Request $request) {
		$this->request = $request;
	}

	public function setDomain($domain){
		$this->domain = $domain;
		return $this;
	}

	/** @return $this */
	public function launch($pattern, $handlerClass) {
		if ($this->match($pattern)) {
			/** @var \Eternity\Application\WebSite $handler */
			$handler = ServiceContainer::get($handlerClass);
			$handler->run();
			die();
		}
		return $this;
	}

	/** @return $this */
	public function reroute($pattern, $target) {
		if ($this->match($pattern)) {
			$url = 'http://' . $target . $this->request->getPathInfo() . ($this->request->getQueryString() ? '?' . $this->request->getQueryString() : '');
			if (is_callable($target)) {
				header('location:' . $target($this->request));
			} else {
				header('location:' . $url);
			}
			die();
		}
		return $this;
	}

	protected function match($pattern){
		if($pattern === '.' || $pattern === '' || is_null($pattern)) $pattern = $this->domain;
		if(substr($pattern,-1) === '.') $pattern .= $this->domain;


		return fnmatch($pattern, $this->request->getHost());
	}

}