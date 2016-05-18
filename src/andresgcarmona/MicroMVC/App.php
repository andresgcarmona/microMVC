<?php

namespace andresgcarmona\MicroMVC;

use andresgcarmona\MicroMVC\FrontController;

class App {
	private $_basePath;
	private $_appPath;
	private $_controllersPath;
	private $_modelsPath;
	private $_viewsPath;
	private $_config;
	private $_frontController;

	public function __construct($basePath = null){
		if($basePath) $this->_basePath = $basePath;

		$this->_appPath = str_replace('//', '/', $this->_basePath . '/App');
		$this->_controllersPath = str_replace('//', '/', '/App/Controllers/');
		$this->_modelsPath = str_replace('//', '/', '/App/Models/');
		$this->_viewsPath = str_replace('//', '/', '/App/Views/');
	}

	public function start(){
		$this->_frontController = FrontController::getInstance();
		$this->_frontController->setApp($this);
		$this->_frontController->init()->run();
	}

	public function getConfig($key){
		return $this->_config[$key];
	}

	public function getControllersPath() {
		return $this->_controllersPath;
	}

	public function getModelsPath() {
		return $this->_modelsPath;
	}

	public function getViewsPath() {
		return $this->_viewsPath;
	}

	public function getAppPath() {
		return $this->_appPath;
	}

	public function getBasePath() {
		return $this->_basePath;
	}
}