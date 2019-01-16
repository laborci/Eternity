<?php namespace RedFox\Database\Filter;

use RedFox\Database\PDOConnection\AbstractPDOConnection;

abstract class AbstractFilterBuilder {

	protected $connection;

	public function __construct(AbstractPDOConnection $connection) { $this->connection = $connection; }
	abstract public function getSql(array $where): string;
	abstract protected function getSqlFromArray(array $filter): string;

}