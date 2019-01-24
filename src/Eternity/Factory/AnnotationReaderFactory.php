<?php namespace Eternity\Factory;

use Eternity\ServiceManager\Service;
use Minime\Annotations\Cache\FileCache;
use Minime\Annotations\Parser;
use Minime\Annotations\Reader;

class AnnotationReaderFactory {

	protected $config;
	public function __construct(AnnotationReaderConfigInterface $config) { $this->config = $config; }

	public function factory(): Reader {
		$reader = new Reader(new Parser(), new FileCache($this->config::cache()));
		return $reader;
	}
}