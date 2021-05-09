<?php


namespace Model\Entities;


class Lecturer {
    use \Library\Entity;
    use \Library\Shared;

    /**
     * Lecturer constructor.
     * @param string $guid
     * @param string $firstname
     * @param string $lastname
     * @param string $patronymic
     * @param string $position
     */
    public function __construct(private string $guid, public string $firstname,
                                public string $lastname, public string $patronymic, public string $position) {
    }

    public static function search(int $lesson, int $limit = 0):self|array|null {
        $result = [];
        $db = self::getDB();
        foreach($db->select([
            'LessonLecturer' => []
        ])->where([
            'LessonLecturer' => [
                'lesson' => $lesson
            ]
        ])->many($limit) as $lecturer) {
            $class = __CLASS__;
            $result[] = new $class($lecturer['lecturer'], 'Микола', 'Годовиченко', 'Анатолієвич', 'стример');
        }

        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

}