<?php namespace RedFox\PDOEntityDTOConverter;

use RedFox\Database\PDOConnection\AbstractPDOConnection;
use RedFox\Entity\Model;

class PDOEntityDTOConverterFactory {

	static function factory(AbstractPDOConnection $connection, Model $model):AbstractPDOEntityDTOConverter{
		$driver = $connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
		switch ($driver){
			case 'mysql':
				return new MysqlPDOEntityDTOConverter($model);
				break;
		}
	}

}