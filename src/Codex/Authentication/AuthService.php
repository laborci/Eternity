<?php namespace Codex\Authentication;


class AuthService implements AuthServiceInterface {

	protected $container;
	protected $repository;

	public function __construct(AuthContainerInterface $container, AuthenticableRepositoryInterface $repository) {
		$this->container = $container;
		$this->repository = $repository;
	}

	public function isAuthenticated(): bool {
		return (bool)$this->container->getUserId();
	}

	public function getAuthenticatedId():int {
		return $this->container->getUserId();
	}

	public function getAuthenticated(): AuthenticableInterface {
		return $this->repository->authLookup($this->container->getUserId());
	}

	public function login($login, $password, $permission = null): bool {
		try {
			$user = $this->repository->authLoginLookup($login);
		}catch (\Throwable $exception){
			return false;
		}
		dump('PASS:', $user->checkPassword($password));
		dump('PERM:',  $user->checkPermission($permission));
		if ($user->checkPassword($password) && ( $user->checkPermission($permission) || is_null($permission))) {
			$this->registerLogin($user);
			return true;
		} else {
			return false;
		}
	}

	public function registerLogin(AuthenticableInterface $user){
		$this->container->setUserId( $user->getId() );
	}

	public function checkPermission($permission): bool {
		if(!$this->isAuthenticated()) return false;
		return $this->getAuthenticated()->checkPermission($permission);
	}

	public function logout() {
		$this->container->forget();
	}

}