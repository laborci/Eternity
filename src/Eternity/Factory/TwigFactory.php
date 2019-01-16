<?php namespace Eternity\Factory;


use Eternity\Application\Config;

class TwigFactory {
	static public function factory():\Twig_Environment{
		$loader = new \Twig_Loader_Filesystem();
		foreach(Config::get('twig')['sources'] as $namespace=>$path)
		$loader->addPath($path,$namespace);
		$twig = new \Twig_Environment($loader, array(
			'cache' => Config::get('twig')['cache'],
			'debug'=>Config::get('dev-mode')
		));
		return $twig;
	}
}