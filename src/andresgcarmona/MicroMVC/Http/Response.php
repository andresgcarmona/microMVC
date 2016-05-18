<?php

namespace andresgcarmona\MicroMVC\Http;

use ArrayAccess;

class Response implements ArrayAccess {
	protected $_response;

	public function __construct($response) {
		$this->_response = $response;
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
	
	public function getResponse() {
		return $this->_response;
	}
	
	public function setResponse($response) {
		$this->_response = $response;
		return $this;
	}

	public function send() {
		if(!empty($this->_response)) {
			if(is_object($this->_response)) {
				if(method_exists($this->_response, 'render')) {
					echo $this->_response->render();
				}
				else{
					echo $this->_response;
				}
			}
			else{
				echo $this->_response;
			}

			$this->_clean();
		}
	}

	public function __toString() {
		return $this->_response;
	}

	public static function redirect($url, $referer = NULL) {
		if(empty($url)) {
			throw new Exception('Url');
		}

		if(!empty($referer)) {
			if(session_id() == '' || !isset($_SESSION)) {
				session_start();
			}
			
			$_SESSION['referer'] = $referer; 
		}
		
		$httpHost = $_SERVER['HTTP_HOST'];
		
		if(strpos($httpHost, $url) == FALSE) {
			$url = $httpHost . '/'. $url;
		}
		
		$url = str_replace('//', '/', $url);
		header('Location: http://' . $url);
	}

	public static function back($flashData = NULL) {
		if(!empty($flashData)) {
			if(session_id() == '' || !isset($_SESSION)) session_start();
			$_SESSION['flash'] = $flashData;
		}

		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}

	private function _clean() {
		if(isset($_SESSION)) {
			if(isset($_SESSION['flash'])) {
				unset($_SESSION['flash']);
			}
		}
	}
}