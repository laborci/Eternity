<?php namespace Codex\Authentication;


interface AuthServiceInterface {

	public function isAuthenticated():bool;
	public function getAuthenticated(): AuthenticableInterface;
	public function getAuthenticatedId():int;
	public function login($login, $password, $permission = null): bool;
	public function checkPermission($permission): bool;
	public function logout();
	public function registerLogin(AuthenticableInterface $user);

}