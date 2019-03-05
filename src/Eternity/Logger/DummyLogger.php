<?php namespace Eternity\Logger;

class DummyLogger implements ErrorHandlerRegistratorInterface, LoggerInterface {
	public function __invoke($data){}
	public function dump($data){}
	public function sql($sql){}
}