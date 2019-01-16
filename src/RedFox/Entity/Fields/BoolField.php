<?php namespace RedFox\Entity\Fields;

/**
 * @datatype "bool"
 */
class BoolField extends \RedFox\Entity\Field {

	public function importFromDTO($value){
		return (bool)$value;
	}

	public function set($value){ return (bool)$value; }

}