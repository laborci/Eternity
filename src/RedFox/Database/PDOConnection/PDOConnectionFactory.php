<?php namespace RedFox\Database\PDOConnection;

use PDO;

class PDOConnectionFactory {

	static function factory($url, $sqlHook=null): AbstractPDOConnection {
		$url = parse_url($url);
		switch ($url['scheme']) {
			case 'mysql':
				$connection = static::mysql($url);
				break;
			default:
				$connection = null;
		}
		if(!is_null($sqlHook)) $connection->setSqlHook($sqlHook);
		return $connection;
	}

	static function mysql($url): AbstractPDOConnection {
		parse_str($url['query'], $options);

		$host = $url['host'];
		$database = trim($url['path'], '/');
		$user = $url['user'];
		$password = $url['pass'];
		$port = $url['port'];
		$charset = array_key_exists('charset', $options) ? $options['charset'] : 'utf-8';

		$dsn = 'mysql:host=' . $host . ';dbname=' . $database . ';port=' . $port . ';charset=' . $charset;

		$connection = new MysqlPDOConnection($dsn, $user, $password);

		$connection->setAttribute(PDO::ATTR_PERSISTENT, true);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$connection->query("SET CHARACTER SET $charset");

		return $connection;
	}

}