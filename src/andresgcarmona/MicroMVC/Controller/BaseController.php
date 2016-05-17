<?php namespace andresgcarmona\MicroMVC\Controller;

abstract class BaseController {
	private $_request;

	public function setRequest($request){
		$this->_request = $request;	
	}
}