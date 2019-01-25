<?php namespace Eternity\Factory;


use Application\Config;
use Eternity\ServiceManager\Service;

class TwigFactory {

	protected $config;
	public function __construct(TwigConfigInterface $config) { $this->config = $config; }

	public function factory(): \Twig_Environment {
		$loader = new \Twig_Loader_Filesystem();
		foreach ($this->config::sources() as $namespace => $path) {
			$loader->addPath($path, $namespace);
		}
		$twig = new \Twig_Environment($loader, [
			'cache' => $this->config::cache(),
			'debug' => $this->config::debug(),
		]);
		if ($this->config::debug()) {
			$twig->addExtension(new \Twig_Extension_Debug());
		}
		return $twig;
	}
}