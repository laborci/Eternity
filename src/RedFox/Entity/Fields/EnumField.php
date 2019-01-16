<?php namespace RedFox\Entity\Fields;

/**
 * @datatype "string"
 */
class EnumField extends \RedFox\Entity\Field {

	protected $options;

	public function __construct($name, $options) {
		parent::__construct($name);
		$this->options = $options;
	}

	public function getOptions(){ return $this->options; }
	public function set($value) {
		if(!in_array($value, $this->options)) {
			throw new \Exception('Enum Field type set error');
		}
		return $value;
	}

}