<?php namespace RedFox\EntityGenerator;


interface EntityGeneratorConfigInterface {
	static public function path();
	static public function databases();
	static public function default_database();
}