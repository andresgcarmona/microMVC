<?php

namespace andresgcarmona\MicroMVC\Model;

use andresgcarmona\MicroMVC\Database\QueryBuilder;
use ArrayAccess;

abstract class ModelBase implements ArrayAccess {
	protected static $booted = [];

	protected $table;
	protected $connection;
	protected $attributes = [];
	protected $idColumn = 'id';
	
	private $_columns = [];
	private $_data;

	public function __construct($attributes = []) {
		$this->boot();
		if(is_array($attributes)) $this->fill($attributes);
	}

	protected function boot() {
		$class = get_class($this);
		if(!isset(static::$booted[$class])) {
			static::$booted[$class] = true;
			static::resolveConnection();
		}
	}

	protected static function resolveConnection() {
		$model = new static;
		return $model->setConnection(QueryBuilder::create($model));
	}

	public function fill($attributes) {
		foreach($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
	}

	/**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function setAttribute($key, $value) {
        if($this->hasSetMutator($key)) {
            $method = 'set' . Str::studly($key) . 'Attribute';

            return $this->{$method}($value);
        }

        $this->attributes[$key] = $value;
    }

    public function getAttribute($key) {
        return $this->attributes[$key];
    }

    public function __get($attribute) {
        return $this->attributes[$attribute];
    }

    public function __set($attribute, $value) {
        $this->attributes[$attribute] = $value;
    }

    public function __isset($attribute) {
        return isset($this->attributes[$attribute]);
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator($key) {
        return method_exists($this, 'set' . $key . 'Attribute');
    }

    /**
     * Determine if the given attribute exists.
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

    public function setConnection($connection) {
    	$this->connection = $connection;
    	return $this;
    }

    public function getConnection() {
    	return $this->connection;
    }

    public function find($id){
		return $this->qb->select($columns)->get();
	}

    /*************************/
	
	public function getId(){
		$idColumn = $this->getIdColumn();
		
		return intval($this->$idColumn);
	}
	
	public function getInfoFromDB(){
		$idColumn = $this->getIdColumn();
		
		if(!empty($idColumn)){
			if(!is_array($idColumn)){
				$idColumn = $this->idColumn;
				
				return $this->qb->select()
						 		->where($this->getTableName() . '.' . $idColumn, '=', $this->$idColumn)
						 		->get();
			}
			else{
				$query = $this->qb->select();
				
				foreach($idColumn as $ic){
					$query->where($this->getTableName() . '.' . $ic, '=', $this->$ic);
				}
				
				return $query->get();
			} 
		}
	}

	public function getIdColumn(){
		return $this->idColumn;
	}
	
	public function getTableName(){
		return $this->table;
	}
	
	public function getColumns(){
		return $this->_getTableColumns();
	}
	
	public function getEmptyColumns(){
		$columns = $this->getColumns();
		$data = array();
		
		foreach($columns as $column){
			$data[$column] = '';
		}
		
		return $data;
	}

	public function findById($id, $columns = array()){
		if(empty($id)){
			throw new Exception('Id column not found.');
		}

		return $this->qb->select($columns)
						->where($this->idColumn, '=', $id)
						->get();
	}

	public function count(){
		return $this->qb->count();
	}

	public function save(){
		return $this->qb->update();
	}
	
	public function insert($returnId = FALSE){
		$result = $this->qb->insert();
		
		if(!empty($returnId)){
			$cId = $this->getIdColumn();
			
			if(!is_array($cId)){
				$this->$cId = $this->qb->getLastId($cId);
			}
			
			if(!empty($this->$cId)){
				$this->_updateData();
			}
		}
		
		return $result;
	}
	
	public function delete($where){
		return $this->qb->delete($where);
	}

	public function getData(){
		$data = array();
		$this->_refreshData();
		
		if(!empty($this->_data))
			$data = &$this->_data;
		else
			$data = &$this->getArrayCopy();
		
		return $data;
	}

	public function setData($data){
		ini_set("pcre.backtrack_limit", "4000000");
		ini_set("memory_limit", "400M");
		if(is_array($data) && count($data) > 0){
			foreach($data as $k => $v){
				$this[$k] = $v;
			}

			$this->_data = $this->getArrayCopy();
		}
	}
	
	public function setTable($table){
		if(!empty($table)){
			$this->table = $table;
			$this->qb = new QueryBuilder($this->table, $this);
		}
		
		return $this;
	}
	
	public function toArray(){
		return $this->getArrayCopy();
	}
	
	public function __toString(){
		return '';
	}
	
	private function _getTableColumns(){
		if(!empty($this->_columns)){
			return $this->_columns;
		}
		else{
			return Database::getInstance()->getTableColumns($this->getTableName());
		}
	}

	private function _setRelations(){
		if(!empty($this->hasMany)){
			
		}
	}
	
	private function _refreshData(){
		$columns = $this->getColumns();
		
		foreach($columns as $column){
			if(!empty($this->$column)){
				$this->_data[$column] = $this->$column;
			}
		}
	}
	
	private function _updateData(){
		$info = $this->getInfoFromDB();
		
		if(!empty($info)){
			$this->setData($info->getData());
		}
	}
}
