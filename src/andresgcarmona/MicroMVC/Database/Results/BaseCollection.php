<?php

namespace andresgcarmona\MicroMVC\Database\Results;

use andresgcarmona\MicroMVC\Support\Arrayable;
use ArrayAccess;
use ArrayIterator;
use Iterator;
use mysqli_result;

abstract class BaseCollection implements Iterator, ArrayAccess, Arrayable {
	protected $items = [];
	protected $position;

	public function __construct($items = []) {
		$items = is_null($items) ? [] : $this->getArrayableItems($items);
		$this->items = (array) $items;
		$this->position = 0;
	}

	protected function getArrayableItems($items) {
		if($items instanceof Collection) {
			$items = $items->all();
		}
		elseif ($items instanceof Arrayable) {
			$items = $items->toArray();
		}

		return $items;
	}

	public static function make($items = null){
		return new static($items);
	}

	public function all() {
		return $this->items;
	}

	public function each(callable $callback) {
		array_map($callback, $this->items);
		return $this;
	}

	public function first(callable $callback = null, $default = null) {
		if(is_null($callback)) {
			return count($this->items) > 0 ? reset($this->items) : null;
		}

		return array_first($this->items, $callback, $default);
	}

	public function get($key, $default = null) {
		if($this->offsetExists($key)) {
			return $this->items[$key];
		}

		return value($default);
	}

	public function has($key){
		return $this->offsetExists($key);
	}

	public function isEmpty() {
		return empty($this->items);
	}

	public function keys() {
		return new static(array_keys($this->items));
	}

	public function last() {
		return count($this->items) > 0 ? end($this->items) : null;
	}

	public function lists($value, $key = null) {
		return array_pluck($this->items, $value, $key);
	}

	public function map(callable $callback) {
		return new static(array_map($callback, $this->items, array_keys($this->items)));
	}

	public function pop() {
		return array_pop($this->items);
	}

	public function prepend($value)	{
		array_unshift($this->items, $value);
	}

	public function push($value) {
		$this->offsetSet(null, $value);
	}

	public function pull($key, $default = null) {
		return array_pull($this->items, $key, $default);
	}

	public function put($key, $value) {
		$this->offsetSet($key, $value);
	}

	public function shuffle() {
		shuffle($this->items);
		return $this;
	}

	public function sort(callable $callback) {
		uasort($this->items, $callback);
		return $this;
	}

	public function toArray() {
		return array_map(function($value) {
			return $value instanceof Arrayable ? $value->toArray() : $value;

		}, $this->items);
	}

	public function toJson($options = 0) {
		return json_encode($this->toArray(), $options);
	}

	public function getIterator() {
		return new ArrayIterator($this->items);
	}

	public function count() {
		return count($this->items);
	}

	public function __toString() {
		return $this->toJson();
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

    public function current() {
        return $this->items[$this->position];
    }

    public function next() {
        ++$this->position;
    }

    public function key() {
        return $this->position;
    }

    public function valid() {
        return array_key_exists($this->position, $this->items);
    }

    public function rewind() {
        $this->position = 0;
    }
}