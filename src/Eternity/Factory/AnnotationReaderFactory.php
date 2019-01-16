<?php namespace Eternity\Factory;

use Eternity\Application\Config;
use Minime\Annotations\Cache\FileCache;
use Minime\Annotations\Parser;
use Minime\Annotations\Reader;

class AnnotationReaderFactory {

	static public function factory():Reader{

		$reader = new Reader(new Parser(), new FileCache(Config::get('annotationreader')['cache']));

		return $reader;
	}
}