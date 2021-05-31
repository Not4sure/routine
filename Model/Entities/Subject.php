<?php

namespace Model\Entities;

class Subject {
    use \Library\Shared;
    use \Library\Entity;

    public function __construct(public string $guid, public string $name){}

    // Todo: дописать
    public static function search(string $guid,  int $limit = 0): self|array|null {
        $result = [];

        $class = __CLASS__;
        $result[] = new $class($guid, $guid);

        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

}