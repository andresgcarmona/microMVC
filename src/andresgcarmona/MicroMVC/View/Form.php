<?php

namespace andresgcarmona\MicroMVC\View;

use andresgcarmona\MicroMVC\Validation\Form\Validator;
use andresgcarmona\MicroMVC\Http\Request;

abstract class Form extends View {
	protected $data;
	protected $validators = [];

	public function __construct($view, $data = []) {
		parent::__construct($view, $data);
	}

	public function setValidators($validators = []){
		$this->validators = $validators;
	}

	public function validate(Request $request){
		$validators = $this->validators;
		$data = $request->getParams();

		$validatorsFields = array_keys($validators);
		$fields = array_keys($data);
		$values = array_values($data);
		$errors = [];

		foreach($validatorsFields as $field){
			if(in_array($field, $validatorsFields)){
				$rules = $validators[$field];
				
				foreach($rules as $rule){
					if(is_array($rule)){
						
					}
					elseif(is_string($rule)){
						$result = Validator::validate($rule, $field, $data);

						if(!empty($result)){
							$errors[$field][$rule] = $result; 
						}
					}
				}
			}
		}

		$this->errors = $errors;

		return empty($errors);
	}
}