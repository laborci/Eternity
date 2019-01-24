<?php namespace Entity\{{name}};

class {{name}} extends \RedFox\Entity\Entity implements Helpers\EntityInterface{

	use Helpers\EntityTrait;

	public function __toString(){ return (string) $this->id; }

}