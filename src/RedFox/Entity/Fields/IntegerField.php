<?php namespace RedFox\Entity\Fields;

/**
 * @datatype "int"
 */
class IntegerField extends \Redfox\Entity\Field{
	public function set($value){ return intval($value); }
}