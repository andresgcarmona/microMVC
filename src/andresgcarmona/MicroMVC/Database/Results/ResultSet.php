<?php

namespace andresgcarmona\MicroMVC\Database\Results;

use andresgcarmona\MicroMVC\Model\BaseModel;

class ResultSet extends BaseCollection {
	protected $results;
	protected $model;

	protected static $booted = [];

	public function __construct($results = null, $model = null) {
		$this->results = $results;
		$this->model   = $model;

		$this->boot();
	}

	protected function boot() {
		$class = get_class($this);
		if(!isset(static::$booted[$class])) {
			static::$booted[$class] = true;
			$this->hydrate();
		}
	}

	public function hydrate() {
		if(!is_null($this->results)) {
			$items = $this->results->fetch_all(MYSQLI_ASSOC);
			foreach($items as $item) {
				$this->items[] = new $this->model($item);
			}
		}

		return $this;
	}

	public function toCollection() {
		$this->boot();
		return new Collection($this->items);
	}

	public function current() {		
		return parent::current();
	}
}