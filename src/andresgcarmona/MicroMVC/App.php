<?php namespace andresgcarmona\MicroMVC;

use andresgcarmona\MicroMVC\FrontController;

class App {
	private $_basePath;
	private $_config;
	private $_frontController;

	public function __construct($basePath = null){
		if($basePath) $this->_basePath = $basePath;
	}

	public function start(){
		$this->_frontController = FrontController::getInstance();
		$this->_frontController->setApp($this);
		$this->_frontController->init()->run();
	}

	public function getConfig($key){
		return $this->_config[$key];
	}
}