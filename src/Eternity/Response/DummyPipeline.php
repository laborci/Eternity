<?php namespace Eternity\Response;

use Eternity\ServiceManager\ServiceContainer;
use http\Env\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DummyPipeline extends Pipeline {
	public function __construct() {}
	public function __invoke() {}
	public function run() {}
	public function pipe($responderClass, $arguments = []):Pipeline {return $this;}
	public function redirect($url, $statusCode = 301) {}
}
