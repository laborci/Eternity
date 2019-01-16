<?php namespace Eternity\Response;

use Eternity\Response\Responder\Redirect;
use Eternity\ServiceManager\ServiceContainer;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Pipeline {

	/** @var ParameterBag */
	protected $pathBag;
	/** @var array|bool */
	protected $pipeline = [];
	/** @var Response */
	protected $response;
	/** @var Request */
	protected $request;
	/** @var ParameterBag */
	protected $dataBag;
	/** @var PipelineRunner */
	protected $runner;

	public function __construct($pipeline = [], $pathParameters = [], Request $request = null) {
		$this->request = $request;
		$this->pathBag = new ParameterBag($pathParameters);
		$this->dataBag = new ParameterBag();
		$this->response = new Response();
		$this->pipeline = [];
		foreach ($pipeline as $segment) $this->pipe($segment['responderClass'], $segment['arguments']);
	}

	public function __invoke() { $this->run(); }

	public function run() {
		$runner = new PipelineRunner(
			$this->request,
			$this->response,
			$this->pathBag,
			$this->dataBag,
			$this->pipeline
		);
		$runner();
		$this->response->send();
		die();
	}

	public function pipe($responderClass, $arguments = []): self {
		$this->pipeline[] = ['responderClass' => $responderClass, 'arguments' => $arguments];
		return $this;
	}

	public function redirect($url, $status = 302) {
		$this->pipe(Redirect::class, ['url'=>$url, 'status'=>$status]);
		return $this;
	}


}