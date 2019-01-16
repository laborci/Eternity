<?php namespace RedFox\Entity\Fields;

/**
 * @datatype "string"
 */
class StringField extends \RedFox\Entity\Field {

	public function set($value) { return (string)$value; }

}