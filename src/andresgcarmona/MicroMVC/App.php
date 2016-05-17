<?php namespace andresgcarmona/MicroMVC;

use \FrontController;

class App {
	private $_config;
	private $_frontController;
	protected static $_instance;

	public function __construct($config){
		$this->_config = $config;
	}

	public function start(){
		var_dump('here'); exit;
		$this->_frontController = FrontController::getInstance();
		$this->_frontController->setApp($this);
		$this->_frontController->init()->run();
	}

	public function getConfig($key){
		return $this->_config[$key];
	}
}