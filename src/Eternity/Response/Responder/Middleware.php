<?php namespace Eternity\Response\Responder;

use Eternity\Response\Segment;
use Eternity\ServiceManager\ServiceContainer;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class Middleware extends Segment {

	final public function __invoke($method = 'run') {
		if (method_exists($this, 'shutDown'))register_shutdown_function([$this, 'shutDown']);
		$this->$method();
	}
}