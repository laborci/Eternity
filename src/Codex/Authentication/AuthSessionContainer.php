<?php namespace Codex\Authentication;

use Eternity\Session\Container;

class AuthSessionContainer extends Container implements AuthContainerInterface {

	public $userId;
	public function setUserId($userId) { $this->userId = $userId; }
	public function getUserId() { return $this->userId; }

}