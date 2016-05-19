<?php

namespace andresgcarmona\MicroMVC\Database\Results;

class Collection extends BaseCollection {

	public function add($item) {
		$this->items[] = $item;
		return $this;
	}
}