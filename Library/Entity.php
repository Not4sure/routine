<?php
/**
 * System utilities
 *
 * @author Serhii Shkrabak
 * @package Library\Entity
 */
namespace Library;
trait Entity {

	private array $_changed;

	public function set(Array $fields):self {
		foreach ($fields as $field => $value) {
			$this->_changed[$field] = $value;
			$this->$field = $value;
		}
		return $this;
	}
}