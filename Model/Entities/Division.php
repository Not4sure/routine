<?php

namespace Model\Entities;


class Division {
    use \Library\Entity;
    use \Library\Shared;

    public function __construct(public int $id, public string $name){}

    public static function getDivisions() {
        $result = [];
        foreach(self::getDB()->select(['Division' => ['name']])->many() as $division) {
            $result[] = $division['name'];
        }
        return $result;
    }

    public static function search(int $id = 0, string $name = '', int $limit = 0): self|array|null {
        $result = [];
        $request = self::getDB()->select(['Division' => []]);
        $query = [];

        $filter = $id ? 'id' : ($name ? 'name' : null);

        if($filter) $query = $request->where(['Division' =>[$filter => $$filter]])->many();

        foreach($query as $division) {
            $class = __CLASS__;
            $result[] = new $class($division['id'], $division['name']);
        }

        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

}