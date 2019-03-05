<?php namespace Eternity\Application;

use Eternity\ServiceManager\{Service, SharedService};


abstract class WebSite implements SharedService{

	use Service;

	abstract public function run();
}
