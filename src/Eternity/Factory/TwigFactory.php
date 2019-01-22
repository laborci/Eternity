<?php namespace Eternity\Factory;


use Eternity\Application\Config;

class TwigFactory {
	static public function factory(): \Twig_Environment {
		$loader = new \Twig_Loader_Filesystem();
		foreach (Config::get('twig')['sources'] as $namespace => $path)
			$loader->addPath($path, $namespace);
		$twig = new \Twig_Environment($loader, [
			'cache' => Config::get('twig')['cache'],
			'debug' =>true// Config::get('dev-mode'),
		]);
//		if (Config::get('dev-mode'))
			$twig->addExtension(new \Twig_Extension_Debug());
		return $twig;
	}
}