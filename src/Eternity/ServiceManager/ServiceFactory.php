<?php namespace Eternity\ServiceManager;


class ServiceFactory{

	protected $name;
	protected $shared = false;
	protected $factory = null;
	protected $service = null;
	protected $type;
	protected $sharedService = null;

	const SERVICE = 1;
	const FACTORY = 2;

	public function __construct(string $name) {
		$this->name = $name;
	}

	public function shared(){
		$this->shared = true;
		return $this;
	}

	public function factory(callable $factory){
		$this->type = static::FACTORY;
		$this->factory = $factory;
		return $this;
	}

	public function service($service){
		$this->type = static::SERVICE;
		$this->service = $service;
		return $this;
	}

	public function get(){
		if(!is_null($this->sharedService)){
			return $this->sharedService;
		}elseif ($this->type === static::FACTORY){
			$service = ($this->factory)($this->name);
		}else{
			$class = $this->service;
			$reflect = new \ReflectionClass($class);
			$constructor = $reflect->getConstructor();
			$arguments = [];

			if(!is_null($constructor)) {
				$parameters = $constructor->getParameters();
				foreach ($parameters as $parameter) {
					$arguments[] = ServiceContainer::get(strval($parameter->getType()));
				}
			}
			$service = new $class(...$arguments);
		}

		if($this->shared){
			$this->sharedService = $service;
		}

		return $service;
	}

}