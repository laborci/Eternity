<?php namespace RedFox\Database\PDOConnection;

use PDO;
use RedFox\Database\Repository\AbstractRepository;
use RedFox\Database\SmartAccess\AbstractSmartAccess;
use RedFox\Database\Filter\AbstractFilterBuilder;
use RedFox\Database\Finder\AbstractFinder;

abstract class AbstractPDOConnection extends \PDO {
	abstract public function quoteValue($subject, bool $addQuotationMarks = true): string;
	abstract public function quoteArray(array $array, bool $addQuotationMarks = true): array;
	abstract public function escapeSQLEntity($subject): string;
	abstract public function escapeSQLEntities(array $array): array;
	abstract public function applySQLParameters(string $sql, array $sqlParams = []): string;
	abstract public function createFinder():AbstractFinder;
	abstract public function createSmartAccess():AbstractSmartAccess;
	abstract public function createRepository(string $table):AbstractRepository;
	abstract public function createFilterBuilder():AbstractFilterBuilder;
	public function query($sql) {
		$statement = parent::query($sql);
		dump($statement);
		return $statement;
	}
}