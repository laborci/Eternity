<?php namespace Eternity\Application;

use Eternity\Logger\ErrorHandlerRegistratorInterface;
use Eternity\Router\DomainRouter;
use Eternity\ServiceManager\{Service, SharedService};

abstract class WebApp implements SharedService {

	use Service;

	protected $domainRouter;

	public function __construct(ErrorHandlerRegistratorInterface $errorHandlerRegistrator, \Eternity\Routing\DomainRouter $domainRouter) {
		$errorHandlerRegistrator->registerErrorHandlers();
		$this->domainRouter = $domainRouter;
	}

	abstract protected function route(\Eternity\Routing\DomainRouter $domainRouter);

	public function run(){ $this->route($this->domainRouter); }

}