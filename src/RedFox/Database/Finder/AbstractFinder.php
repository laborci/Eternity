<?php namespace RedFox\Database\Finder;

use RedFox\Database\PDOConnection\AbstractPDOConnection;
use RedFox\Database\Filter\Filter;

abstract class AbstractFinder {

	/** @var AbstractPDOConnection */
	protected $connection;
	/** @var callable */
	protected $converter;
	protected $select;
	/** @var Filter */
	protected $filter;
	protected $from;
	protected $limit;
	protected $offset;
	protected $order = [];

	public function __construct(AbstractPDOConnection $connection) {
		$this->connection = $connection;
	}

	public function setConverter(callable $converter = null): self {
		$this->converter = $converter;
		return $this;
	}

	public function select(string $sql, ...$sqlParams): self {
		$this->select = $this->connection->applySQLParameters($sql, $sqlParams);
		return $this;
	}

	public function from(string $sql, ...$sqlParams): self {
		$this->from = $this->connection->applySQLParameters($sql, $sqlParams);
		return $this;
	}

	public function where(Filter $filter = null): self {
		$this->filter = $filter;
		return $this;
	}

	#region ORDER
	function order($order): self {
		if (is_array($order)) foreach ($order as $field => $dir) $this->order[] = $this->connection->escapeSQLEntity($field) . ' ' . $dir;
		else $this->order[] = $order;
		return $this;
	}
	function asc($field): self { return $this->order($this->connection->escapeSQLEntity($field) . ' ASC'); }
	function desc($field): self { return $this->order($this->connection->escapeSQLEntity($field) . ' DESC'); }
	function ascIf(bool $cond, string $field): self { return $cond ? $this->asc($field) : $this; }
	function descIf(bool $cond, string $field): self { return $cond ? $this->desc($field) : $this; }
	function orderIf(bool $cond, $order): self { return $cond ? $this->order($order) : $this; }
	#endregion

	public function collect($limit = null, $offset = null, &$count = null): array {
		$records = $this->collectRecords($limit, $offset, $count);
		$records = $this->convertRecords($records);
		return $records;
	}

	public function pick() { return $this->convertRecord($this->pickRecord()); }

	public function collectPage($pageSize, $page, &$count = 0): array {
		$records = $this->collectPageRecords($pageSize, $page, $count);
		$records = $this->convertRecords($records);
		return $records;
	}

	abstract public function fetch($fetchmode = \PDO::FETCH_ASSOC):array ;

	protected function pickRecord() {
		$records = $this->collectRecords(1, null);
		if ($records) {
			$record = array_shift($records);
			return $record;
		} else return null;
	}

	protected function collectPageRecords($pageSize, $page, &$count = 0): array {
		$pageSize = abs(intval($pageSize));
		$page = abs(intval($page));
		$records = $this->collectRecords($pageSize, $pageSize * ($page - 1), $count);
		return $records;
	}

	abstract protected function collectRecords($limit = null, $offset = null, &$count = false): array;

	abstract public function count(): int;

	abstract public function buildSQL($doCounting = false): string;

	protected function convertRecord($record) {
		$converter = $this->converter;
		return is_null($converter) || is_null($record) ? $record : $converter($record);
	}

	protected function convertRecords($records) {
		if (!is_null($this->converter)) {
			foreach ($records as $key => $record) {
				$converter = $this->converter;
				$records[$key] = $converter($record);
			}
		}
		return $records;
	}
}
