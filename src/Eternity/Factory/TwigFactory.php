<?php namespace Eternity\Factory;


use Application\Config;

class TwigFactory {
	static public function factory(): \Twig_Environment {
		$loader = new \Twig_Loader_Filesystem();
		foreach (Config::twig()::sources as $namespace => $path)
			$loader->addPath($path, $namespace);
		$twig = new \Twig_Environment($loader, [
			'cache' => Config::twig()::cache,
			'debug' => Config::env()::dev_mode(),
		]);
		if (Config::env()::dev_mode())
			$twig->addExtension(new \Twig_Extension_Debug());
		return $twig;
	}
}