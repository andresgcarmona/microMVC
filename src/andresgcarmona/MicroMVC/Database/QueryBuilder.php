<?php

namespace andresgcarmona\MicroMVC\Database;

use andresgcarmona\MicroMVC\Model\BaseModel;
use andresgcarmona\MicroMVC\Database\Statement;
use andresgcarmona\MicroMVC\Database\Results\Collection;
use andresgcarmona\MicroMVC\Database\Results\ResultSet;
use Exception;

class QueryBuilder {
	protected $db;
    protected $model;
    protected $query;
    protected $table;
    protected $stmt;

    protected $wheres = [];
    protected $whereValues = [];
    protected $fields = [];
    protected $count  = null;

	public function __construct(BaseModel $model) {
		$this->model = $model;
		$this->db    = Database::getInstance();
		$this->table = $model->getTable();
		$this->query = null;
    }
    
    public static function create($model) {
        return new self($model);
    }

    public function getQuery() {
    	return $this->query;
    }

    public function getWheres() {
    	return $this->wheres;
    }

    public function select($fields) {
    	$this->fields = $fields;
    	return $this;
    }

    public function where($field, $comparator = null, $value = null) {
    	if(empty($field) || empty($comparator) || !isset($value)){
			throw new Exception('Argument missing.');
		}

		if(!is_array($field)) {
			$this->wheres[$field] = [$comparator => $value];
		}
		else {
			foreach($field as $f => $v) {
				if(!is_string($f)) throw new Exception('Bad query.');
				$this->wheres[$f] = ['=' => $v];
			}
		}

		return $this;
    }

    public function count($field = null) {
    	if(empty($field)) $field = '*';
    	$this->count = "COUNT($field) AS count";

    	return $this->get();
    }

    public function first() {
    	return $this->get()->first();
    }

    public function get() {
    	$this->_buildQuery();

    	if(empty($this->query)) throw new Exception('Bad query.');

    	$this->stmt = new Statement($this->db->getConnection());
    	$results = $this->stmt->prepare($this->query)
		    			      ->bindParams($this->whereValues)
		    			      ->execute()
		    			      ->getResults();

		if($results) {
			return new ResultSet($results, $this->model);
		}
    }

    public function insert($attributes) {
        $fields = [];
        $params = [];
        $values = [];

        $this->query = "INSERT INTO {$this->table} ";

        $fields = array_keys($attributes);
        $values = array_values($attributes);
        $params = array_pad([], count($values), '?');

        $this->query .= "(" . implode(', ', $fields) . ") VALUES ";
        $this->query .= "(" . implode(', ', $params) . ")";

        $this->stmt = new Statement($this->db->getConnection());
        $result = $this->stmt->prepare($this->query)
                   ->bindParams($values)
                   ->execute();
    }

    private function _initQuery() {
    	if(is_null($this->query)) $this->query = 'SELECT ';
    	if(is_null($this->count)) $this->query .= 'SQL_CALC_FOUND_ROWS ';

    	return $this;
    }

    private function _setFields() {
    	$fields = [];

    	//Prepend $this->count before every other field.
    	if(!is_null($this->count)) $fields[] = $this->count;

    	if(empty($this->fields) && is_null($this->count)) {
    		$fields[] = "*";
    	}
    	else {
    		if(is_string($this->fields)) {
    			$fields[] = $this->fields;
    		}
    		elseif(is_array($this->fields)) {
	    		foreach($this->fields as $key => $field) {
	    			if(is_string($key)) {
	    				$fields[] = $key . ' AS ' . $field;
	    			}
	    			else {
	    				$fields[] = $field;
	    			}
	    		}
	    	}
    	}

		$this->query .= implode(', ', $fields) . ' ';

    	return $this;
    }

    private function _setTable() {
    	$this->query .= "FROM {$this->table}";
    	return $this;
    }

    private function _setConditions() {
    	$wheres = [];

    	if(!empty($this->wheres)) {
    		foreach($this->wheres as $field => $condition) {
    			$c = array_keys($condition)[0];
    			$v = array_values($condition)[0];

    			if(is_string($v))
    				$wheres[] = $field . $c . '?';
    			else
    				$wheres[] = $field . $c . '?';
    			
    			$this->whereValues[] = $v;
    		}
    	}

    	if(!empty($wheres)) $this->query .= ' WHERE ' . implode(' AND ', $wheres);

    	return $this;
    }

    private function _buildQuery() {
    	return $this->_initQuery()
    				->_setFields()
    		 	    ->_setTable()
    		 	    ->_setConditions();
    }
}