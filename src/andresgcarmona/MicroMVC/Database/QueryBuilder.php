<?php

namespace andresgcarmona\MicroMVC\Database;

use andresgcarmona\MicroMVC\Model\ModelBase;

class QueryBuilder {
	protected $db;
    protected $model;

	public function __construct(ModelBase $model) {
        $this->model = $model;
        $this->db = Database::getInstance();
    }
    
    public static function create($model) {
        return new self($model);
    }
}