<?php namespace RedFox\Entity\Fields;

/**
 * @datatype \DateTime
 */
class DateTimeField extends \RedFox\Entity\Field {

	public function importFromDTO($value) { return (is_null($value)) ? null : new \DateTime($value); }

	/**
	 * @param \DateTime $value
	 * @return string
	 */
	public function exportToDTO($value) { return is_null($value) ? null : $value->format('c'); }

	public function set($value) {
		if (is_string($value)) $value = $this->importFromDTO($value);
		if (get_class($value) !== 'DateTime' && !is_null($value)) throw new \Exception('Date Field type set error');
		return $value;
	}
}