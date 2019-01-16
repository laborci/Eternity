<?php namespace RedFox\Entity\Fields;

/**
 * @datatype "float"
 */
class FloatField extends \RedFox\Entity\Field {

	public function getDataType(){return 'float';}

	public function set($value) { return floatval($value); }

}