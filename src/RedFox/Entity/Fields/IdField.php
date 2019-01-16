<?php namespace RedFox\Entity\Fields;

/**
 * @datatype "int"
 */
class IdField extends \RedFox\Entity\Field {

	public function importFromDTO($value) { return is_null($value) || $value == 0 ? null : intval($value); }
	public function set($value) { return is_null($value) || $value == 0 ? null : intval($value); }

}