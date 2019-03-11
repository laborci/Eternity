<?php

function dump(...$messages){
	if (env('DEV_MODE'))
		foreach ($messages as $message)
			Eternity\ServiceManager\ServiceContainer::get(\Eternity\Logger\LoggerInterface::class)->dump($message);
}
function env($key = null){ return is_null($key) ? $_ENV : array_key_exists($key, $_ENV) ? $_ENV[$key] : null; }
function setenv($key, $value){
	if (is_string($value) || is_bool($value) || is_numeric($value))
		putenv($key . "=" . $value);
	$_ENV[$key] = $value;
}
function unsetenv($key){ unset ($_ENV[$key]); }