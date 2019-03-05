<?php namespace Eternity\Application;

use Eternity\ServiceManager\Service;
use Eternity\ServiceManager\SharedService;

class ConfigDeprecated implements \ArrayAccess, SharedService {

	use Service;

	protected $container;

	public static function get($offset) { return Config::Service()[$offset]; }
	public function __construct() { $this->container = include getenv('CONFIG'); }
	public function offsetSet($offset, $value) { trigger_error('Do not modify the ' . __CLASS__ . '!'); }
	public function offsetUnset($offset) { trigger_error('Do not modify the ' . __CLASS__ . '!'); }
	public function offsetExists($offset) { return isset($this->container[$offset]); }
	public function offsetGet($offset) { return isset($this->container[$offset]) ? $this->container[$offset] : trigger_error(__CLASS__ . ' value "' . $offset . '" has not been set'); }
}
