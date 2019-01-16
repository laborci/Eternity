<?php namespace Eternity\Application;

use Eternity\ServiceManager\{Service, SharedService};


abstract class WebApp implements SharedService{

	use Service;

	abstract public function run();
}
