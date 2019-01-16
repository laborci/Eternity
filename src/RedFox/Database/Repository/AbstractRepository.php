<?php namespace RedFox\Database\Repository;

//TODO: this is mysql specific

use RedFox\Database\Filter\Filter;
use RedFox\Database\PDOConnection\AbstractPDOConnection;

abstract class AbstractRepository {


	/** @var string */
	protected $table;
	protected $escTable;
	/** @var AbstractPDOConnection */
	protected $connection;

	public function __construct($connection, $table) {
		$this->table = $table;
		$this->connection = $connection;
		$this->escTable = $this->escapeSQLEntity($table);
	}

	public function search(Filter $filter = null) { return $this->connection->createFinder()->select($this->escTable . '.*')->from($this->escTable)->where($filter); }
	public function pick(int $id) { return $this->search(Filter::where('id = $1', $id))->pick(); }
	public function collect(array $ids) { return $this->search(Filter::where('id IN ($1)', $ids))->collect(); }
	public function count(Filter $filter = null) { return $this->connection->createFinder()->from($this->escTable)->where($filter)->count(); }
	public function save($record) { return $record['id'] ? $this->update($record) : $this->insert($record); }

	abstract public function insert(array $record, $insertIgnore = false);
	abstract public function update($record): int;
	abstract public function delete(int $id);

	protected function quoteValue($value) { return $this->connection->quoteValue($value); }
	protected function escapeSQLEntity($value) { return $this->connection->escapeSQLEntity($value); }
	protected function query($sql) { return $this->connection->query($sql); }

}
