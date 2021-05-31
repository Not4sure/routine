<?php

namespace Model\Entities;


class Division {
    use \Library\Entity;
    use \Library\Shared;

    public function __construct(public string $name, public int $id = 0){}

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

        if($filter) $query = $request->where(['Division' => [$filter => $$filter]])->many();

        foreach($query as $division) {
            $class = __CLASS__;
            $result[] = new $class($division['name'], $division['id']);
        }

        return $limit == 1 ? ($result[0] ?? null) : $result;
    }

    public function save(): self {
        $db = $this->getDB();

        if(!$this->id){
            $this->id = $db->insert([
                'Division' => [
                    'name' => $this->name
                ]
            ])->run(true)->storage['inserted'];
        } else {
            $db -> update('Division', $this->_changed )
                -> where(['Division'=> ['id' => $this->id]])
                -> run();
        }
        return $this;
    }

}