<?php

namespace andresgcarmona\MicroMVC\View;

use andresgcarmona\MicroMVC\FrontController;

class View extends BaseView {
	protected $_content;
	protected $_view;
	protected $_data;
	protected $_layout;

	private $_fc;

	public function __construct($view, $data = []) {
		parent::__construct($data);

		$this->_view = $view;
		$this->_data = $data;

		$this->_fc = FrontController::getInstance();
		$controller = $this->_fc->getRequest()->getController();
	}

	public function render($headers = array()){
		header('Content-type: text/html');
		header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
		header('Pragma: no-cache'); // HTTP 1.0.
		header('Expires: 0'); // Proxies.
		
		return $this->_doRender();
	}

	public function show($template){
		$v = new View($template, $this->_data);
		return $v->render();
	}
	
	public function partial($template, $data){
		$v = new View($template, $data);
		return $v->render();
	}
	
	public function getViewScript(){
		return $this->_view;
	}

	public function getData(){
		return $this->_data;
	}
	
	public function setData($data){
		$this->_data = $data;
		return $this;
	}

	public function __toString(){
		return $this->render();
	}
	
	private function _assignData(){
		if(!empty($this->_data)){
			foreach($this->_data as $key => $value){
				$this->$key = $value;
			}
		}
	}

	private function _doRender(){
		$file = str_replace('.', '/', $this->_view) . '.php';
		$templateBase =  $this->_fc->getApp()->getAppPath() . '/Views/';
		$templateFileName = $templateBase . $file;

		ob_start();
		include $templateFileName;
		return ob_get_clean();
	}
}