<?php
namespace Model\Entities;

class Lesson {
    use \Library\Shared;
	use \Library\Entity;

    public static function search(Int $id = 0, ?String $room = null, ?String $subject = null,
        ?string $division = null, ?\DateTime $time = null, ?\DateInterval $interval = null,
        ?string $lecturer = null, ?String $type = null, Int $limit = 0):self|array|null{
        
        $result = [];
        $rawsql = '';
        $db = self::getDB();                                    //Достаём базу данных
        $lessons = $db -> select(['Lesson' => []]);              //Достаём из базы данных таблицу lesson

        if($interval && $time)
            $end  = $time->add($interval);

        if($id)
            $filters['id'] = $id;
        else {
            foreach (['room', 'subject', 'type'] as $var)
                if ($$var)
                    $filters[$var] = $$var;
            if($division)
                $rawsql .= "`core`.`Lesson`.`id` = (SELECT `core`.`Lesson_Division`.`lesson_id` FROM `core`.`Lesson_Division` WHERE `core`.`Lesson_Division`.`division` = '$division')";
            if($lecturer)
                $rawsql .= "`core`.`Lesson`.`id` = (SELECT `core`.`Lesson_Lecturer`.`lesson_id` FROM `core`.`Lesson_Lecturer` WHERE `core`.`Lesson_Lecturer`.`lecturer` = '$lecturer')";
        }

        if(!empty($filters))                                    
            $lessons->where(['Lesson'=> $filters], raw: $rawsql);


        foreach($lessons->many($limit) as $lesson) {
            $class = __CLASS__;                                                                                 //класс lesson
            $lecturers = \Model\Entities\Lecturer::search(lesson: $lesson['id']);
            $divisions = \Model\Entities\Division::search(lesson: $lesson['id']);

            if(isset($lesson['room']))
                $room = \Model\Entities\Room::search($lesson['room'], limit: 1);
            $subject = \Model\Entities\Subject::search($lesson['subject'], limit: 1);
            $time = date_create_from_format('Y-m-d H:i:s', $lesson['time']);
            $result[] = new $class($lecturers, $divisions, $subject, $time,         //создаём экземпляр класса
                $room, $lesson['id'], $lesson['type'], $lesson['comment']);
        }
        return $limit == 1 ? ($result[0] ?? null) : $result;
    }
//Допилил save
    public function save():self {       
		$db = $this->db;
		if(!$this->id) {
            $insert = [
				'subject' => $this->subject,
				'day' => $this->day,
				'number' => $this->number,
                'type' => $this->type,
			];
			if ($this->room) {
				$insert['room'] = $this->room;
			}
			if ($this->week) {
				$insert['week'] = $this->week;
			}  
            if ($this->comment) {
				$insert['comment'] = $this->comment;
			}        
			$this->id = $db -> insert([
				'Lesson' => $insert
			])->run(true)->storage['inserted'];;
        }
        if ($this->_changed)
			$db -> update('Lesson', $this->_changed )
				-> where(['Lesson'=> ['id' => $this->id]])
				-> run();
		return $this;
	}

    public function __construct(public array $lecturers, public array $divisions, public Subject $subject,
                                public \DateTime $time, public ?Room $room = null, public Int $id = 0,
                                public ?String $type = null, public ?String $comment = null) {
		$this->db = $this->getDB();
        // if(!$this->db){
            
        //     $this->lecturers = $lecturers;
        //     $this->$groups = $groups;
        // }
	}
}