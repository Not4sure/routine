<?php
namespace Model\Entities;

class Lesson {
    use \Library\Shared;
	use \Library\Entity;

    public static function search(Int $id = 0, ?String $room = null, ?String $subject = null,
                                  ?string $division = null, int $since = 0, int $till = 0,
                                  ?string $lecturer = null, ?String $type = null, Int $limit = 0):self|array|null{
        
        $result = [];
        $rawSql = '';
        $db = self::getDB();                                    //Достаём базу данных
        $lessons = $db -> select(['Lesson' => []]);              //Достаём из базы данных таблицу lesson
        if(!$till && $since) $till = $since;


        if($id)
            $filters['id'] = $id;
        else {
            foreach (['room', 'subject', 'type'] as $var)
                if ($$var)
                    $filters[$var] = $$var;
            if($division)
                $rawSql .= "AND `core`.`Lesson`.`id` in (SELECT `core`.`LessonDivision`.`lesson` FROM 
                    `core`.`LessonDivision` WHERE `core`.`LessonDivision`.`division` = 
                        (SELECT `core`.`Division`.`id` FROM `core`.`Division` WHERE `core`.`Division`.`name` = '$division'))";
            if($lecturer)
                $rawSql .= "AND `core`.`Lesson`.`id` in (SELECT `core`.`LessonLecturer`.`lesson` FROM `core`.`LessonLecturer` WHERE `core`.`LessonLecturer`.`lecturer` = '$lecturer')";
            if($since)
                $rawSql .= "AND `core`.`Lesson`.`time` between from_unixtime($since) AND from_unixtime($till)";
        }

        $lessons->where(isset($filters) ? ['Lesson'=> $filters] : [], raw: $rawSql ? substr($rawSql, 4) : '');

        foreach($lessons->many($limit) as $lesson) {
            $class = __CLASS__;         //класс lesson
            $room = null;


            $lecturers = \Model\Entities\Lecturer::search(lesson: $lesson['id']);
            $divisions = \Model\Entities\Division::search(lesson: $lesson['id']);
            $subject = \Model\Entities\Subject::search($lesson['subject'], limit: 1);

            $time = date_create_from_format('Y-m-d H:i:s', $lesson['time']);
            if($lesson['room'])
                $room = \Model\Entities\Room::search($lesson['room'], limit: 1);

            $result[] = new $class($lecturers, $divisions, $subject, $time,         //создаём экземпляр класса
                $room, $lesson['id'], $lesson['type'], $lesson['comment']);
        }
        return $limit == 1 ? ($result[0] ?? null) : $result;
    }

    // Todo: переделать для нормального сохранения с учетом изменений в бд
    public function save():self {       
		$db = $this->db;
		if(!$this->id) {
            $insert = [
				'subject' => $this->subject,
                'time' => $this->time->format('Y-m-d H:i:s'),
                'type' => $this->type,
			];
//			if ($this->room) {
//				$insert['room'] = $this->room;
//			}
//            if ($this->comment) {
//				$insert['comment'] = $this->comment;
//			}
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
                                public ?string $type = null, public ?string $comment = null) {
		$this->db = $this->getDB();
        // if(!$this->db){
            
        //     $this->lecturers = $lecturers;
        //     $this->$groups = $groups;
        // }
	}
}