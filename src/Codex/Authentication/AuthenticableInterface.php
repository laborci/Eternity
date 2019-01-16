<?php namespace Codex\Authentication;

interface AuthenticableInterface {
	public function getId():int;
	public function checkPassword($password):bool;
	public function checkPermission($permission):bool;
	public function getAdminAvatar();

}