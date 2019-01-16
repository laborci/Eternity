<?php namespace Eternity\Application;

use Eternity\Router\DomainRouter;
use Eternity\ServiceManager\{Service, SharedService};

abstract class Application implements SharedService {

	use Service;

	public function run() {
		switch (getenv('CONTEXT')) {
			case 'WEB':
				$this->web();
				break;
			case 'CLI':
				$this->cli();
				break;
		}
	}


	abstract protected function web();
	abstract protected function cli();
}