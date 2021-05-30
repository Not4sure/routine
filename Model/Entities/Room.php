<?php

namespace Model\Entities;

class Room {
    use \Library\Shared;
    use \Library\Entity;

    public function __construct(public string $guid, public string $name){}

    // Todo: дописать
    public static function search(string $guid,  int $limit = 0): self|array|null {
        $result = [];

        $class = __CLASS__;
        $result[] = new $class($guid, '401ф');

        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

}