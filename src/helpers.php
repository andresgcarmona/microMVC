<?php

if(!function_exists('dd')) {
	function dd($var) {
		var_dump($var);
		die;
	}
}

if(!function_exists('value')) {
	function value($value) {
		return $value instanceof Closure ? $value() : $value;
	}
}