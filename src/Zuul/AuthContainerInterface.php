<?php namespace Zuul;

interface AuthContainerInterface {

	public function setUserId($userId);
	public function getUserId();
	public function forget();


}