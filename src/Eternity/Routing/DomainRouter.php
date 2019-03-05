<?php namespace Eternity\Routing;

use Eternity\Application\WebApp;
use Eternity\ServiceManager\Service;
use Eternity\ServiceManager\ServiceContainer;
use Eternity\ServiceManager\SharedService;
use Symfony\Component\HttpFoundation\Request;

class DomainRouter implements SharedService {

	use Service;

	protected $request;
	protected $superDomain = '';

	public function __construct(Request $request) {
		$this->request = $request;
	}

	public function setSuperDomain($superDomain){
		$this->superDomain = $superDomain;
	}

	public function launch($pattern, $handlerClass) {
		if (fnmatch($pattern.$this->superDomain, $this->request->getHost())) {
			/** @var WebApp $handler */
			$handler = ServiceContainer::get($handlerClass);
			$handler->run();
		}
	}

	public function reroute($pattern, $target) {
		if (fnmatch($pattern.$this->superDomain, $this->request->getHost())) {
			$url = 'http://'.$target.$this->request->getPathInfo().($this->request->getQueryString() ? '?'.$this->request->getQueryString() : '');
			header('location:'.$url);
		}
	}

}