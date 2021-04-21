<?php


namespace Model\Entities;


class Lecturer {
    use \Library\Entity;

    private string $guid;
    public string $firstname;
    public string $lastname;
    public string $patronymic;
    public string $position;

    /**
     * Lecturer constructor.
     * @param string $guid
     */
    public function __construct(string $guid) {
        $this->guid = $guid;
        // from employee service
        $this->firstname = 'Микола';
        $this->lastname = 'Годовиченко';
        $this->patronymic = 'Анатолієвич';
        $this->position = 'стример';
    }

    public static function search(int $lesson, int $limit = 0):self|array|null {
        $result = [];
        $db = self::getDB();
        foreach($db->select([
            'Lesson_Lecturer' => []
        ])->where([
            'Lesson_Lecturer' => [
                'lesson_id' => $lesson
            ]
        ])->many($limit) as $lecturer) {
            $class = __CLASS__;
            $result[] = new $class($lecturer['lecturer']);
        }

        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

}