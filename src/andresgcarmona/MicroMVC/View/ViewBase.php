<?php

namespace andresgcarmona\MicroMVC\View;

use andresgcarmona\MicroMVC\FrontController;
use ArrayAccess;
use ArrayObject;

abstract class ViewBase extends ArrayObject implements ArrayAccess {
	protected $data = [];

	public function __construct($data = []){
        $fc = FrontController::getInstance();
        $data = array_merge($data, $fc->getRequest()->getFlashData());

		parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
	}

	public function __get($attribute) {
        return $this->data[$attribute];
    }

    public function __set($attribute, $value) {
        $this->data[$attribute] = $value;
    }

    public function __isset($attribute) {
        return isset($this->data[$attribute]);
    }
}