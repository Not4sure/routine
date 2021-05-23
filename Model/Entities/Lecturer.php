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
    public function __construct(public string $id, public string $firstname,
                                public string $lastname, public string $patronymic, public string $position, private string $guid = '') {
    }


    // Todo: дописать
    public static function search(int $id, int $limit = 0):self|array|null {
        $result = [];

        $class = __CLASS__;
        $result[] = new $class($id, 'Микола', 'Годовиченко', 'Анатолієвич', 'стример');


        return $limit == 1 ? ($result[0] ?? null) : $result;
    }

}