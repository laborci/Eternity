<?php namespace Eternity\Logger;

use Entity\Config\Config;
use Eternity\ServiceManager\Service;
use Eternity\ServiceManager\SharedService;
use Symfony\Component\HttpFoundation\Request;

interface RemoteLogConfigInterface{
	static public function host();
	static public function port();
}