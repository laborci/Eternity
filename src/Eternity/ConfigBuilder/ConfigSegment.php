<?php namespace Eternity\ConfigBuilder;

class ConfigSegment implements \ArrayAccess {

	public $env = [], $value = [], $interface = null;

	public function __construct(string $interface = null) {
		$this->interface = $interface;
	}

	function interface(string $interface) {
		$this->interface = $interface;
		return $this;
	}
	function env(array $env) {
		$this->env = $env;
		return $this;
	}

	function value(array $value) {
		$this->value = $value;
		return $this;
	}

	public function offsetExists($offset) {
		return (in_array($offset, ['env', 'value', 'interface']));
	}
	public function offsetGet($offset) {
		switch ($offset){
			case 'env': return $this->env; break;
			case 'interface': return $this->interface; break;
			case 'value': return $this->value(); break;
		}
	}
	public function offsetSet($offset, $value) { }
	public function offsetUnset($offset) { }

}