<?php

namespace andresgcarmona\MicroMVC\Database;

use mysqli;

class Statement {
	private $_connection;
	private $_stmt;

	public function __construct(mysqli $connection) {
		$this->_connection = $connection;
		$this->_stmt       = null;
	}

	public function prepare($query) {
		$this->_stmt = $this->_connection->prepare($query);

		if($this->_connection->error) dd($this->_connection->error);
		return $this;
	}

	public function execute() {
    	$this->_stmt->execute();
        if($this->_stmt->error) dd($this->_stmt->error);
        
    	return $this;
    }

    public function bindParams($params) {
    	if(!empty($params))
    		call_user_func_array([$this->_stmt, 'bind_param'], $this->_getBindParams($params));

    	return $this;
    }

    public function getResults() {
    	return $this->_stmt->get_result();
    }

    public function getResultMetadata() {
		$metadata = $this->_stmt->result_metadata();
		$fields   = $metadata->fetch_fields();

		return $fields;
    }

    private function _getBindParams($params) {
    	$wheres = [$this->_getParamsString($params)];
    	foreach($params as &$where) $wheres[] = $where;
    	
    	return $wheres;
    }

    private function _getParamsString($params) {
    	$paramsString = '';

    	if(!empty($params)) {
    		foreach($params as $value) {
    			if(is_string($value)) $paramsString .= 's';
    			else $paramsString .= 'i';
    		}
    	}

    	return $paramsString;
    }
}