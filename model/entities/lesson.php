<?php
namespace Model\Entities;

class Lesson{
    use \Library\Shared;
	use \Library\Entity;

    public static function search(Int $id = 0, ?String $room = null, ?String $subject = null,
        ?String $week = null, ?Int $day = 0, ?Int $number = 0,
        ?String $type = null, ?String $comment = null, Int $limit = 0):self|array|null{         
        
        $result = [];
        $db = self::getDB();                                    //Достаём базу данных
        $lessons = $db -> select(['Lesson' => []]);              //Достаём из базы данных таблицу lesson

        foreach (['id', 'room','number'] as $var)      
            if ($$var)
                $filters[$var] = $$var;
        if(!empty($filters))                                    
            $lessons->where(['Lesson'=> $filters]);



        foreach ($lessons->many($limit) as $lesson) {
            $class = __CLASS__;                                                                                 //класс lesson
            $lecturers = \Model\Entities\Lecturer::serch(lesson: $lesson['id']);
            $result[] = new $class($lesson['id'], $lesson['room'], $lesson['subject'], $lesson['week'],         //создаём экземпляр класса 
                $lesson['day'], $lesson['number'], $lesson['type'], $lesson['comment'], $lecturers);
        }
        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

    public function save():self {       
		$db = $this->db;
		if(!$this->id) {
            //И где id?
            throw new \Exception('И где id?', 6);
        }
        if ($this->_changed)
			$db -> update('Lesson', $this->_changed )
				-> where(['Lesson'=> ['id' => $this->id]])
				-> run();
		return $this;
	}

    public function __construct(public Int $id = 0, public array $lecturers, public ?String $room = null, public ?String $subject = null,
                                public ?String $week = null, public ?Int $day = 0, public ?Int $number = 0,
                                public ?String $type = null, public ?String $comment = null) {

		$this->db = $this->getDB();
	}
}