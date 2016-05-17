<?php namespace andresgcarmona\MicroMVC;

use \ReflectionClass;
use \Exception;

class FrontController{
	protected $_controller;
	protected $_action;
	protected $_params = array();
	protected $_request;
	protected $_response;
	protected $_app;

	public static $_instance;

	public static function getInstance(){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function init(){
		return $this->_route();	
	}

	public function run(){
		$this->getResponse()->send();
	}

	public function getController(){
		$controller = explode('_', $this->_controller);
		$c = '';

		if(is_array($controller)){
			foreach($controller as $ctrl){
				$c .= ucwords($ctrl);
			}
		}
		
		return $c . 'Controller';
	}

	public function getAction(){
		return $this->_action;
	}

	public function getRequest(){
		return $this->_request;
	}

	public function getParams(){
		return $this->_params;
	}

	public function getResponse(){
		return $this->_response;
	}

	public function getApp(){
		return $this->_app;
	}

	public function setApp($app){
		$this->_app = $app;
		return $this;
	}

	private function __construct(){
		$request = $_SERVER['REQUEST_URI'];

		$parts = explode('/', trim($request, '/'));
		
		$this->_controller = !empty($parts[0]) ? $parts[0] : 'index';
		
		if(isset($parts[1]) && strstr($parts[1], '?') != FALSE){
			$action = explode('?', $parts[1]);
			$this->_action = !empty($action[0]) ? $action[0] : 'index';
			$this->_action = str_replace('-', '_', $this->_action);
		}
		else{
			$this->_action = !empty($parts[1]) ? $parts[1] : 'index';
			$this->_action = str_replace('-', '_', $this->_action);
		}
		
		if(!empty($_POST)){
			$this->_params = $_POST;
		}
		
		if(!empty($parts[2])){
			$params = array_slice($parts, 2, count($parts));
			$keys = array();
			$values = array();
			
			foreach($params as $key => $value){
				if($key % 2 == 0){
					$keys[] = $value;
				}
				else{
					$values[] = $value;
				}
			}
			
			$this->_params = array_merge($this->_params, array_combine($keys, $values));
		}
		
		if(!empty($_SERVER['QUERY_STRING'])){
			$qs = explode('&', $_SERVER['QUERY_STRING']);
			$params = array();
			$values = array();
			
			if(!empty($qs)){
				foreach($qs as $p){
					$parts = explode('=', $p);
					$params[] = $parts[0];
					$values[] = $parts[1];
				}
			}
			
			if(!empty($params) && !empty($values)){
				$this->_params = array_merge($this->_params, array_combine($params, $values));
			}
		}
	}

	private function _route(){
		if(class_exists($this->getController())){
			$reflex = new ReflectionClass($this->getController());
			
			if($reflex->hasMethod($this->getAction())){
				$this->_request = new Request($this->_controller, $this->_action, $this->_params);

				$controller = $reflex->newInstance();
				$controller->setRequest($this->_request);
				
				//Get init method if exists
				if($reflex->hasMethod('init')){
					$init = $reflex->getMethod('init');
					$init->invoke($controller);
				}

				$action = $reflex->getMethod($this->getAction());
				
				//$this->_response = new Response($action->invoke($controller));
			}
			else{
				throw new Exception('Action');
			}
		}
		else{
			throw new Exception('Controller');
		}

		return $this;
	}
}
