<?php

namespace andresgcarmona\MicroMVC\Controller;

use ArrayObject;

abstract class BaseController extends ArrayObject {
	protected $data = [];

	public function __get($attribute) {
        return $this->data[$attribute];
    }

    public function __set($attribute, $value) {
        $this->data[$attribute] = $value;
    }

	public function setRequest($request){
		$this->data['request'] = $request;	
	}
}