<?php namespace Eternity\Response;

use Eternity\ServiceManager\ServiceContainer;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PipelineRunner {

	private $request;
	private $response;
	private $pathBag;
	private $dataBag;
	private $pipeline;

	public function __construct(Request $request,
	                            Response $response,
	                            ParameterBag $pathBag,
	                            ParameterBag $dataBag,
	                            array &$pipeline) {
		$this->request = $request;
		$this->response = $response;
		$this->pathBag = $pathBag;
		$this->dataBag = $dataBag;
		$this->pipeline = $pipeline;
	}

	public function __invoke($responderClass = null, $arguments = []) {
		if(!is_null($responderClass)){
			$segment = ['responderClass'=>$responderClass, 'arguments'=>$arguments];
			$this->pipeline = [];
		}else if (!count($this->pipeline)){
			return;
		} else {
			$segment = array_shift($this->pipeline);
		}
		$class = is_array($segment['responderClass']) ? $segment['responderClass'][0] : $segment['responderClass'];
		$method = is_array($segment['responderClass']) ? $segment['responderClass'][1] : null;
		$arguments = $segment['arguments'];

		/** @var Segment $segmentObject */
		$segmentObject = ServiceContainer::get($class);
		$segmentObject->execute(
			$method,
			$this->request,
			$this->response,
			new ParameterBag($arguments),
			$this->pathBag,
			$this->dataBag,
			$this);
	}
}