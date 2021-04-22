<?php
namespace Model\Entities;

class Lesson {
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

        foreach($lessons->many($limit) as $lesson) {
            $class = __CLASS__;                                                                                 //класс lesson
            $lecturers = \Model\Entities\Lecturer::search(lesson: $lesson['id']);
            $groups = \Model\Entities\Division::search(lesson: $lesson['id']);
            if(isset($lessons['room']))
                $room = \Model\Entities\Room::search($lessons['room']);

            $result[] = new $class($lecturers, $groups, $lesson['subject'], $lesson['week'], $lesson['day'], $lesson['number'],         //создаём экземпляр класса
                $room, $lesson['id'], $lesson['type'], $lesson['comment']);
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

    public function __construct(public array $lecturers, public array $groups, public String $subject, public String $week,
                                public Int $day, public Int $number,  public ?String $room = null, public Int $id = 0,
                                public ?String $type = null, public ?String $comment = null) {

		$this->db = $this->getDB();
	}
}