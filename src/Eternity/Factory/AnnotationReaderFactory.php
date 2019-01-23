<?php namespace Eternity\Factory;

use Application\Config;
use Minime\Annotations\Cache\FileCache;
use Minime\Annotations\Parser;
use Minime\Annotations\Reader;

class AnnotationReaderFactory {

	static public function factory():Reader{

		$reader = new Reader(new Parser(), new FileCache(Config::annotationreader()::cache));

		return $reader;
	}
}