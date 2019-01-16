<?php namespace RedFox\Database\Finder;

use RedFox\Database\Finder\AbstractFinder;

class MysqlFinder extends AbstractFinder {

	protected function collectRecords($limit = null, $offset = null, &$count = false): array {
		$doCounting = !is_null($limit) && $count !== false;
		$this->limit = $limit;
		$this->offset = $offset;
		$sql = $this->buildSQL($doCounting);

		$pdostatement = $this->connection->query($sql);
		$records = $pdostatement->fetchAll($this->connection::FETCH_ASSOC);

		if ($doCounting) {
			$pdostatement = $this->connection->query('SELECT FOUND_ROWS()');
			$count = $pdostatement->fetch($this->connection::FETCH_COLUMN);
		}

		return $records;
	}

	public function count(): int {
		$sql = 'SELECT Count(1) FROM ' . $this->from . ' ' . ($this->filter != null ? ' WHERE ' . $this->filter->getSql($this->connection) . ' ' : '');
		$pdostatement = $this->connection->query($sql);
		return $pdostatement->fetch($this->connection::FETCH_COLUMN);
	}

	protected function buildSQL($doCounting = false): string {
		return
			'SELECT ' .
			($doCounting ? 'SQL_CALC_FOUND_ROWS ' : '') .
			$this->select . ' ' .
			' FROM ' . $this->from . ' ' .
			($this->filter != null ? ' WHERE ' . $this->filter->getSql($this->connection) . ' ' : '') .
			(count($this->order) ? ' ORDER BY ' . join(', ', $this->order) : '') .
			($this->limit ? ' LIMIT ' . $this->limit : '') .
			($this->offset ? ' OFFSET ' . $this->offset : '');
	}

}
