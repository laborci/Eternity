<?php namespace Eternity\Logger;


class DummyLogger {
	public function __invoke($data){}
	public function dump($data){}
	public function sql($sql){}
}