<?php
namespace Model\Entities;

class Lesson {
    use \Library\Shared;
	use \Library\Entity;

    public static function search(Int $id = 0, ?string $room = null, ?string $subject = null,
                                  ?string $division = null, int $since = 0, int $till = 0,
                                  ?string $lecturer = null, ?string $type = null, Int $limit = 0):self|array|null{
        
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
               $rawSql .= "AND `core`.`Lesson`.`id` in (SELECT `core`.`LessonLecturer`.`lesson` FROM 
                    `core`.`LessonLecturer` WHERE `core`.`LessonLecturer`.`lecturer` = 
                        (SELECT `core`.`Lecturer`.`id` FROM `core`.`Lecturer` WHERE `core`.`Lecturer`.`guid` = '$lecturer'))";
            if($since)
                $rawSql .= "AND `core`.`Lesson`.`time` between from_unixtime($since) AND from_unixtime($till)";
        }

        $lessons->where(isset($filters) ? ['Lesson'=> $filters] : [], raw: $rawSql ? substr($rawSql, 4) : '');

        foreach($lessons->many($limit) as $lesson) {
            $class = __CLASS__;         //класс lesson
            $room = null;
            $lecturers = [];
            $divisions = [];

            foreach($db->select(['LessonDivision' => ['division']])->where(['LessonDivision' => ['lesson' => $lesson['id']]])->many() as $query)
                $divisions[] = Division::search(id: $query['division'], limit: 1);

            foreach($db->select(['LessonLecturer' => ['lecturer']])->where(['LessonLecturer' => ['lesson' => $lesson['id']]])->many() as $query)
                $lecturers[] = Lecturer::search(id: $query['lecturer'], limit: 1);

            $subject = Subject::search(guid: $lesson['subject'], limit: 1);

            $time = date_create_from_format('Y-m-d H:i:s', $lesson['time']);
            if($lesson['room'])
                $room = Room::search(guid: $lesson['room'], limit: 1);

            $result[] = new $class($lecturers, $divisions, $subject, $time,
                $room, $lesson['id'], $lesson['type'], $lesson['comment']);
        }
        return $limit == 1 ? ($result[0] ?? null) : $result;
    }

    public function save():self {       
		$db = $this->db;
		// If there's no such entity in db
		if(!$this->id) {
            $insert = [
				'subject' => $this->subject->guid,
                'time' => $this->time->format('Y-m-d H:i:s'),
                'type' => $this->type,
                'comment' => $this->comment,
                'room' => $this->room->guid
			];
            $this->id = $db -> insert([
                'Lesson' => $insert
            ])->run(true)->storage['inserted'];

            foreach($this->divisions as $division) {
                $db -> insert([
                    'LessonDivision' => [
                        'lesson' => $this->id,
                        'division' => $division->id
                    ]
                ])->run();
            }
            foreach($this->lecturers as $lecturer) {
                $db -> insert([
                    'LessonLecturer' => [
                        'lesson' => $this->id,
                        'lecturer' => $lecturer->guid
                    ]
                ])->run();
            }
        }

		// Todo: тут надо доделать
        foreach (['lecturer', 'division'] as $index){
            $field = $index. 's';
            if(isset($this->_changed[$field])){
                $table = 'Lesson'. ucfirst($index);
                $db->delete($table)->where(filters: [$table => [$index => $this->id]])->run();
                foreach($this->_changed[$field] as $entity)
                    $db->insert([$table => [
                        'lesson' => $this->id,
                        $index => $entity->id
                    ]]);
                unset($this->_changed[$field]);
            }
        }
        if($this->_changed && !empty($this->_changed)){
            $db -> update('Lesson', $this->_changed )
                -> where(['Lesson'=> ['id' => $this->id]])
                -> run();
        }
		return $this;
	}

    public function __construct(public array $lecturers, public array $divisions, public Subject $subject,
                                public \DateTime $time, public ?Room $room = null, public int $id = 0,
                                public ?string $type = null, public ?string $comment = null) {
		$this->db = $this->getDB();
	}
}