<?php namespace Eternity\Response;

use Eternity\Response\Responder\Redirect;
use Eternity\ServiceManager\ServiceContainer;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ServerBag;

abstract class Segment {

	private $nextSegment;

	private $request;
	private $response;
	private $argumentsBag;
	private $pathBag;
	private $dataBag;
	/** @var PipelineRunner */
	private $runner;

	final public function execute(
		$method,
		Request $request,
		Response $response,
		ParameterBag $argumentsBag,
		ParameterBag $pathBag,
		ParameterBag $dataBag,
		PipelineRunner $runner) {
		$this->request = $request;
		$this->response = $response;
		$this->argumentsBag = $argumentsBag;
		$this->pathBag = $pathBag;
		$this->dataBag = $dataBag;
		$this->runner = $runner;
		is_null($method) ? $this() : $this($method);
	}

	protected function next(){ ($this->runner)(); }
	protected function redirect($url, $status = 302) { $this->break(Redirect::class, ['url'=>$url, 'status'=>$status]); }
	protected function break($responderClass, $arguments=[]){ ($this->runner)($responderClass, $arguments); }

	abstract public function __invoke($method = null);

	final protected function getRequest(): Request { return $this->request; }
	final protected function getResponse(): Response { return $this->response; }
	final protected function getPathBag(): ParameterBag { return $this->pathBag; }
	final protected function getDataBag(): ParameterBag { return $this->dataBag; }
	final protected function getArgumentsBag(): ParameterBag { return $this->argumentsBag; }
	final protected function getRequestBag(): ParameterBag { return $this->getRequest()->request; }
	final protected function getQueryBag(): ParameterBag { return $this->getRequest()->query; }
	final protected function getAttributesBag(): ParameterBag { return $this->getRequest()->attributes; }
	final protected function getHeadersBag(): HeaderBag { return $this->getRequest()->headers; }
	final protected function getServerBag(): ServerBag { return $this->getRequest()->server; }
	final protected function getCookiesBag(): ParameterBag { return $this->getRequest()->cookies; }
	final protected function getFileBag(): FileBag { return $this->getRequest()->files; }

}