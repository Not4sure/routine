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

			$this->_changed[$field] = gettype($value) == 'object' ? match(get_class($value)) {
			    'Subject', 'Room' => $value->guid,
                '\Datetime' => "from_unixtime({$value->getTimestamp()})",
                default => $value
            } : $value;
			$this->$field = $value;
		}
		return $this;
	}
}