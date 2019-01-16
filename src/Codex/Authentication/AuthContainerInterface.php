<?php namespace Codex\Authentication;

interface AuthContainerInterface {

	public function setUserId($userId);
	public function getUserId();
	public function forget();


}