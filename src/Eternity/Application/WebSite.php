<?php namespace Eternity\Application;

use Eternity\Routing\Router;
use Eternity\ServiceManager\{Service, SharedService};


abstract class WebSite implements SharedService{

	use Service;

	protected $router;

	public function __construct(Router $router) {
		session_start();
		$this->router = $router;
	}

	public function run(){
		$this->route($this->router);
		die();
	}

	abstract protected function route(Router $router);
}
