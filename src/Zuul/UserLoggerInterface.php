<?php namespace Zuul;

Interface UserLoggerInterface {

	const ADMINLOGIN = 'admin-login';
	const ADMINLOGOUT = 'admin-logout';

	public function log($userId, $type, $description = null);

}