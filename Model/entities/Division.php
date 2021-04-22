<?php

namespace Model\Entities;


class Division {
    use \Library\Entity;
    use \Library\Shared;

    public function __construct(public string $guid, public string $name){}

    public static function search(string $lesson,  int $limit = 0): self|array|null {
        $result = [];
        $db = self::getDB();

        foreach($db->select([
            'Lesson_Division' => []
        ])->where([
            'Lesson_Division' => [
                'lesson_id' => $lesson
            ]
        ])->many($limit) as $division) {
            $class = __CLASS__;
            $result[] = new $class($division['division'], 'УП-191');
        }

        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

}