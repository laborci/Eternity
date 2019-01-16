<?php namespace RedFox\Database\PDOConnection;

use RedFox\Database\Filter\{AbstractFilterBuilder, MysqlFilterBuilder};
use RedFox\Database\Finder\{AbstractFinder, MysqlFinder};
use RedFox\Database\Repository\{AbstractRepository, MysqlRepository};
use RedFox\Database\SmartAccess\{AbstractSmartAccess, MysqlSmartAccess};

class MysqlPDOConnection extends AbstractPDOConnection {
	public function quoteValue($subject, bool $addQuotationMarks = true): string { return $subject === null ? 'NULL' : ($addQuotationMarks ? $this->quote($subject) : trim($this->quote($subject), "'")); }
	public function quoteArray(array $array, bool $addQuotationMarks = true): array { return array_map(function ($val) use ($addQuotationMarks) { return $this->quote($val, $addQuotationMarks); }, $array); }
	public function escapeSQLEntity($subject): string { return '`' . $subject . '`'; }
	public function escapeSQLEntities(array $array): array { return array_map(function ($val) { return $this->escapeSQLEntity($val); }, $array); }
	public function applySQLParameters(string $sql, array $sqlParams = []): string {
		if (count($sqlParams)) {
			foreach ($sqlParams as $key => $param) {
				$valueParam = is_array($param) ? join(',', $this->quoteArray($param)) : $this->quote($param);
				$sql = str_replace('$' . ($key + 1), $valueParam, $sql);
				if (!is_array($param)) {
					$sqlEntityParam = $this->escapeSQLEntity($param);
					$sql = str_replace('@' . ($key + 1), $sqlEntityParam, $sql);
				}
			}
		}
		return $sql;
	}
	public function createFinder(): AbstractFinder { return new MysqlFinder($this); }
	public function createSmartAccess(): AbstractSmartAccess { return new MysqlSmartAccess($this); }
	public function createRepository(string $table): AbstractRepository { return new MysqlRepository($this, $table); }
	public function createFilterBuilder(): AbstractFilterBuilder { return new MysqlFilterBuilder($this); }

}

