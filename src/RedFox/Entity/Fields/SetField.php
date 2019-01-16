<?php namespace RedFox\Entity\Fields;

/**
 * @datatype "array"
 */
class SetField extends \RedFox\Entity\Field {

	protected $options;

	public function __construct($name, $options) {
		parent::__construct($name);
		$this->options = $options;
	}

	public function getOptions() { return $this->options; }

	public function set($value) {
		if (count(array_diff($value, $this->options))) {
			throw new \Exception('Set Field type set error');
		}
		return $value;
	}

}