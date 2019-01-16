<?php namespace Eternity\Response\Responder;

use Eternity\ServiceManager\ServiceContainer;
use Minime\Annotations\Reader;

abstract class TwigPageResponder extends PageResponder {

	/** @var \Twig_Environment */
	private $twig;
	protected $annotations;
	private $template;

	public function __construct() {
		$this->twig = ServiceContainer::get(\Twig_Environment::class);
		/** @var Reader $annotationReader */
		$annotationReader = ServiceContainer::get(Reader::class);
		$this->annotations = $annotationReader->getClassAnnotations(get_called_class());
		$this->template = $this->annotations->get('template');
	}

	protected function respond(): string { return $this->twig->render($this->template, $this->createViewModel()); }

	protected function createViewModel(){
		return $this->getDataBag()->all();
	}
}





