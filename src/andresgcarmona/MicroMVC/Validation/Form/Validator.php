<?php

namespace andresgcarmona\MicroMVC\Validation\Form;

class Validator {
	public static function validate($validator, $field, $data){
		switch($validator){
			case 'required':
				return self::required($field, $data);
				break;
		}
	}

	public static function required($field, $data){
		if(empty($data[$field])){
			return 'Campo requerido.';
		}

		return FALSE;
	}
}
