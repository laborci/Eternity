<?php namespace Eternity\Response\Responder;


interface SmartPageResponderConfigInterface {
	static public function client_version();
	static public function cache_path();
}