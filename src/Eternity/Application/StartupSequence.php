<?php namespace Eternity\Application;

use Eternity\Routing\DomainRouter;
use Eternity\ServiceManager\Service;
use Eternity\ServiceManager\SharedService;

include 'facade.php';

class StartupSequence{

	protected $env;

	public function __construct($root, $project_ini = 'etc/ini/project.ini'){

		setenv('ROOT', realpath($root) . '/');
		setenv('CONTEXT', (http_response_code() ? 'WEB' : 'CLI'));
		setenv('PROJECT-INI', parse_ini_file(getenv('ROOT') . $project_ini, true));

		foreach (env('PROJECT-INI')['ENV']['load'] as $envfile){
			if(file_exists($envfile)) {
				$file = include $envfile;
				foreach ($file as $key => $value) {
					if (substr($key, -5) === '@FILE') {
						$key = substr($key, 0, -5);
						$value = file_exists($value) ? file_get_contents($value) : null;
					}
					setenv($key, $value);
				}
			}
		}

		include env('PROJECT-INI')['SERVICE-REGISTRY'];

		date_default_timezone_set(env('TIMEZONE') ?? env('PROJECT-INI')['TIMEZONE']);

		$app = env('APP');
		$app::Service()->run();
	}
	
//
//	public function cli($CLIAPP){
//		if (env('CONTEXT') === 'CLI'){
//			$CLIAPP::Service()->run();
//			die();
//		}
//		return $this;
//	}
//
//	public function web($WEBAPP = \Eternity\Application\WebApp::class):DomainRouter{
//		/** @var \Eternity\Application\WebApp $webapp */
//		$webapp = $WEBAPP::Service();
//		return $webapp->router();
//	}

}