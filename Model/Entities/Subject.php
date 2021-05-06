<?php

namespace Model\Entities;

class Subject {
    use \Library\Shared;
    use \Library\Entity;

    public function __construct(public string $guid, public string $name){}

    public static function search(string $guid,  int $limit = 0): self|array|null {
        $result = [];

        $class = __CLASS__;
        $result[] = new $class($guid, 'Об\'єктно-орієнтоване програмування');

        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

}