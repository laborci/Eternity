<?php namespace Eternity\Response\Responder;

use Eternity\Application\Config;
use Eternity\ServiceManager\ServiceContainer;
use Minime\Annotations\Reader;
use Symfony\Component\HttpFoundation\Request;

abstract class SmartPageResponder extends TwigPageResponder {

	protected $title;
	protected $bodyclass;
	protected $language;

	public function __construct() {
		parent::__construct();
		/** @var \Twig_Environment $twig */
		$twig = ServiceContainer::get(\Twig_Environment::class);
		/** @var \Twig_Loader_Filesystem $loader */
		$loader =$twig->getLoader();
		$loader->addPath(__DIR__.'/smartpage_template', 'smartpage');
	}

	protected function getViewModelData(){
		return $this->getDataBag()->all();
	}

	protected function createViewModel() {
		return [
			'data'      => $this->getViewModelData(),
			'smartpage' => $this->getViewModelSmartPageComponents()
		];
	}

	private function getViewModelSmartPageComponents(){
		return [
			'clientversion' => Config::get('smartresponder-clientversion'),
			'title'         => $this->title ? $this->title : $this->annotations->get('title'),
			'language'      => $this->language ? $this->language : $this->annotations->get('language', getenv('LANGUAGE')),
			'bodyclass'     => $this->bodyclass ? $this->bodyclass : $this->annotations->get('bodyclass'),
			'css'           => $this->annotations->getAsArray('css'),
			'js'            => $this->annotations->getAsArray('js'),
		];
	}
	
}





