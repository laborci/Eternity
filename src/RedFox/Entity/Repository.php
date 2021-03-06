<?php namespace RedFox\Entity;

use Eternity\Cache\MemoryCache;
use Eternity\ServiceManager\ServiceContainer;
use RedFox\Database\Filter\Filter;
use RedFox\PDOEntityDTOConverter\PDOEntityDTOConverterFactory;

abstract class Repository {

	/** @var \RedFox\Database\PDOConnection\AbstractPDOConnection */
	protected $connection;
	/** @var \Eternity\Cache\MemoryCache */
	protected $cache;
	/** @var \RedFox\Database\Repository\AbstractRepository */
	protected $repository;
	/** @var string */
	protected $table;
	/** @var \RedFox\PDOEntityDTOConverter\AbstractPDOEntityDTOConverter  */
	protected $PDODTOConverter;


	public function __construct($model, $connectionClass, $table) {
		$this->connection = ServiceContainer::get($connectionClass);
		$this->PDODTOConverter = PDOEntityDTOConverterFactory::factory($this->connection, $model);
		$this->table = $table;
		$this->repository = $this->connection->createRepository($table);
		$this->cache = new MemoryCache();
	}

	public function getConnection(): \RedFox\Database\PDOConnection\AbstractPDOConnection { return $this->connection; }
	private function addToCache(Entity $object) { $this->cache->add($object, $object->id); }

	/** @return Entity */
	abstract protected function createEntity($dto, $repository = null);
	public function pick(int $id) {
		$cached = $this->cache->get($id);
		if (!is_null($cached)) return $cached;
		$record = $this->repository->pick($id);
		if ($record) {
			$dto = $this->PDODTOConverter->convertToDTO($record);
			$object = $this->createEntity($dto, $this);
			$this->addToCache($object);
			return $object;
		}
		return null;
	}

	public function collect(array $ids) {
		$objects = [];
		$ids = array_unique($ids);
		$requested = count($ids);
		if ($requested == 0) return [];

		foreach ($ids as $index => $id) {
			$cached = $this->cache->get($id);
			if (!is_null($cached)) {
				$objects[] = $cached;
				unset($ids[$index]);
			}
		}
		if (count($ids)) {
			$records = $this->repository->collect($ids);
			foreach ($records as $record) {
				$dto = $this->PDODTOConverter->convertToDTO($record);
				$object = $this->createEntity($dto, $this);
				$this->addToCache($object);
				$objects[] = $object;
			}
		}
		return $objects;
	}

	protected function count(Filter $filter = null) { return $this->repository->count($filter); }

	public function insert(Entity $object) {
		$dto = $object->getDTO();
		$record = $this->PDODTOConverter->convertToPDO($dto);
		return $this->repository->insert($record);
	}

	public function update(Entity $object) {
		$dto = $object->getDTO();
		$record = $this->PDODTOConverter->convertToPDO($dto);
		return $this->repository->update($record);
	}

	public function delete(Entity $object) {
		$this->cache->delete($object->id);
		return $this->repository->delete($object->id);
	}

	public function search(Filter $filter = null) {
		$finder = $this->repository->search($filter);
		return $finder->setConverter(function ($record) {
			$dto = $this->PDODTOConverter->convertToDTO($record);
			$object = $this->createEntity($dto, $this);
			$this->addToCache($object);
			return $object;
		});
	}
}