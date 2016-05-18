<?php

namespace andresgcarmona\MicroMVC\Http;

use ArrayAccess;

class Request implements ArrayAccess {
	private $_controller;
	private $_action;
	private $_params;
	
	public function __construct($controller, $action, $params){
		$this->_controller = $controller;
		$this->_action = $action;
		$this->_params = $params;
	}

	 /* Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value) {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset) {
        unset($this->$offset);
    }

	public function getController(){
		return $this->_controller;
	}

	public function getAction(){
		return $this->_action;
	}

	public function getParam($key){
		if(isset($this->_params[$key])){
			return $this->_scape($this->_params[$key]);
		}
		
		return NULL;
	}

	public function getParams(){
		return $this->_scapeParams();
	}
	
	public function hasParam($key){
		return isset($this->_params[$key]);
	}
	
	public function getURL(){
		$params = $this->getParams();
		$ps = '';
		
		if(isset($params['rc'])){
			unset($params['rc']);
		}
		
		foreach($params as $key => $value){
			$ps .= "/$key/$value";
		}
		
		$ps = str_replace('//', '/', $ps);
		
		return str_replace('//', '/', '/' . $this->getController() . '/' . $this->getAction() . '/' . $ps);
	}

	public function getFlashData() {
		if(session_id() == '' || !isset($_SESSION)) session_start();
		return isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
	}

	private function _scape($item){
		if(is_string($item)){
			return htmlspecialchars($item);
		}
		
		return $item;
	}

	private function _scapeParams(){
		$params = array();

		if(!empty($this->_params)){
			foreach($this->_params as $key => $param){
				$params[$key] = $this->_scape($param);
			}
		}

		return $params;
	}
}