<?php namespace Codex\Authentication;

interface AuthenticableRepositoryInterface {

	public function authLookup($id):AuthenticableInterface;
	public function authLoginLookup($login):AuthenticableInterface;

}