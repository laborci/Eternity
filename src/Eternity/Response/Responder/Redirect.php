<?php namespace Eternity\Response\Responder;

use Eternity\Response\Segment;

class Redirect extends Segment {
	final public function __invoke($method = null) {
		$url = $this->getArgumentsBag()->get('url', '/');
		$status = $this->getArgumentsBag()->get('status', 302);
		$response = $this->getResponse();
		$response->headers->set('Location', $url);
		$response->setStatusCode($status);
	}
}