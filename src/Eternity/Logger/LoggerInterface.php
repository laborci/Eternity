<?php namespace Eternity\Logger;


interface LoggerInterface {
	public function __invoke($data);
	public function dump($data);
	public function sql($sql);
}