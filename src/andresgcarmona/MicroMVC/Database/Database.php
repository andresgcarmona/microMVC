<?php

namespace andresgcarmona\MicroMVC\Database;

use andresgcarmona\MicroMVC\FrontController;
use mysqli;

class Database {
	protected $connection;
	private static $_instance;

	private function __construct(){
		$fc = FrontController::getInstance();
		//$db = $fc->getApp()->getConfig('db');
		
		$db = [
			'host' => 'localhost',
			'user' => 'root',
			'password' => '830508',
			'database' => 'sicet'
		];

		$this->connection = new mysqli($db['host'], $db['user'], $db['password'], $db['database']);
		$this->connection->report_mode = MYSQLI_REPORT_ALL;
		
        if($this->connection->connect_error) die($this->connection->connect_error);
	}

	private function __clone(){}

	public static function getInstance(){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function getConnection() {
		return $this->connection;
	}
}