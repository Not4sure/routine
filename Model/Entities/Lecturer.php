<?php


namespace Model\Entities;


class Lecturer {
    use \Library\Entity;
    use \Library\Shared;

    /**
     * Lecturer constructor.
     * @param string $id
     * @param string $guid
     */
    public function __construct(public int $id = 0, public string $guid = '') {
    }


    // Todo: за мат извени
    public static function search(int $id = 0, string $guid = '', int $limit = 0):self|array|null {
        $result = [];
        $db = self::getDB();
        $filters = [];
        foreach(['id', 'guid'] as $filter)
            if($$filter) $filters[$filter] = $$filter;
        foreach($db->select(['Lecturer' => []])->
                where(['Lecturer' => $filters])->many($limit) as $lecturer) {
            $class = __CLASS__;
            $result[] = new $class($lecturer['id'], $lecturer['guid']);
        }

        return $limit == 1 ? ($result[0] ?? null) : $result;
    }

    public function save(): self {
        $db = $this->getDB();

        if(!$this->id){
            $this->id = $db->insert([
                'Lecturer' => [
                    'guid' => $this->guid
                ]
            ])->run(true)->storage['inserted'];
        }

        return $this;
    }

}