<?php

namespace andresgcarmona\MicroMVC\Model;

use andresgcarmona\MicroMVC\Database\QueryBuilder;
use ArrayAccess;
use Exception;

abstract class BaseModel implements ArrayAccess {
	protected static $booted = [];

	protected $table;
	protected $connection;
	protected $attributes = [];
	//protected $idColumn = 'id';
	
	//private $_columns = [];
	//private $_data;

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
    	return $this->connection;
    }

    public function getConnection() {
    	return $this->connection;
    }

    public function getTable() {
    	return $this->table;
    }

    protected static function resolveConnection() {
		$model = new static;
		return $model->setConnection(QueryBuilder::create($model));
	}

    protected static function query() {
    	return static::resolveConnection();
    }

    public static function find($id){
		return static::where(['id' => $id])->first();
	}

	public static function all() {
		return static::query()->get();
	}

	public static function select($fields) {
		return static::query()->select($fields);
	}

	public static function where($field, $comparator = null, $value = null) {
		return static::query()->where($field, $comparator, $value);
	}

	public static function count($field = null) {
		return static::query()->count($field);	
	}

	public static function create($attributes) {
		return static::query()->insert($attributes);
	}

	public function save() {

	}
}