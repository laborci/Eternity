<?php namespace Eternity\Response\Responder;


use Eternity\Response\Segment;

abstract class PageResponder extends Segment {

	public function __invoke($method = null) {
		$this->prepare();
		if(method_exists($this, 'shutDown')) register_shutdown_function([$this, 'shutDown']);
		$this->getResponse()->setContent($this->respond());
		$this->next();
	}

	protected function prepare(){}

	abstract protected function respond():string;

}