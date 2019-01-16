<?php namespace Eternity\Routing;

use Eternity\Application\Config;
use Eternity\Application\WebApp;
use Eternity\Logger\Logger;
use Eternity\ServiceManager\Service;
use Eternity\ServiceManager\ServiceContainer;
use Eternity\ServiceManager\SharedService;
use Symfony\Component\HttpFoundation\Request;

class DomainRouter implements SharedService {

	use Service;

	protected $host;

	public function __construct(Request $request) {
		$this->host = $request->getHost();
	}

	public function launch($handlerClass, $pattern) {
		if (fnmatch($pattern, $this->host)) {
			/** @var WebApp $handler */
			$handler = ServiceContainer::get($handlerClass);
			$handler->run();
		}
	}

}