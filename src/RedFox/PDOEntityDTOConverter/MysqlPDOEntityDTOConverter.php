<?php namespace RedFox\PDOEntityDTOConverter;


use RedFox\Entity\Fields\BoolField;
use RedFox\Entity\Fields\DateField;
use RedFox\Entity\Fields\DateTimeField;
use RedFox\Entity\Fields\JsonStringField;
use RedFox\Entity\Fields\SetField;

class MysqlPDOEntityDTOConverter extends AbstractPDOEntityDTOConverter {


	protected function toPDO($value, $type) {
		switch ($type) {
			case JsonStringField::class:
				$value = json_encode($value);
				break;
			case DateField::class:
				$value = (new \DateTime($value))->format('Y-m-d');
				break;
			case DateTimeField::class:
				$value = (new \DateTime($value))->format('Y-m-d H:i:s');
				break;
			case BoolField::class:
				$value = (int)$value;
				break;
			case SetField::class:
				$value = join(',', $value);
				break;
		}
		return $value;
	}

	protected function toDTO($value, $type) {
		switch ($type) {
			case JsonStringField::class:
				$value = json_decode($value, true);
				break;
			case DateField::class:
				$value .= ' 00:00:00';
				break;
			case DateTimeField::class:
				$date = new \DateTime($value);
				$date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
				$value = $date->format('c');
				break;
			case BoolField::class:
				$value = (bool)$value;
				break;
			case SetField::class:
				$value = !$value ? [] : explode(',', $value);
				break;
		}
		return $value;
	}
}