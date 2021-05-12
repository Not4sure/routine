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


    // Todo: дописать
    public static function search(string $guid, int $limit = 0):self|array|null {
        $result = [];

        $class = __CLASS__;
        $result[] = new $class($guid, 'Микола', 'Годовиченко', 'Анатолієвич', 'стример');


        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

}