<?php
namespace Model\Entities;
define('FIRST_DAY', mktime(0, 0, 0, month: 2, day: 22, year: 21));

class Routine
{
    use \Library\Entity;

    private string $division;
    private string $type;
    private int $day;
    private string $week;
    private array $lessons;

    /**
     * Routine constructor.
     * @param string $division
     * @param Int $time
     * @param string $type
     */
    public function __construct(string $division, private int $time = 0, string $type = 'day') {
        if($type = 'day' && $time == 0) $this->time = time();
        $this->day = (int)date('N', $this->time);
        $this->week = (($this->time - FIRST_DAY) / (7 * 24 * 60 * 60)) % 2 ? 'p' : 'u';
        $this->division = $division;
        $this->type = $type;
        $this->getLessons();
    }

    private function getLessons() {
        $this->lessons = Lesson::search(division: $this->division);
    }

    public function getText() {
        $text = "Розклад на день $this->day ";
        $text .= $this->week == 'p' ? 'парного ' : 'непарного ';
        $text .= "тижня для групи $this->division\n\n";
        if(isset($this->lessons))
            foreach($this->lessons as $lesson) {
                switch($lesson->type) {
                    case 'lecture':
                        $text .= 'лек. ';
                        break;
                    case 'practice':
                        $text .= 'пр. ';
                        break;
                    case 'lab':
                        $text .= 'лаб. ';
                        break;
                }
                $text .= "{$lesson->subject->name}\n";
                $text .= $lesson->room ? "Аудиторія {$lesson->room->name}" : '';
                foreach($lesson->lecturers as $lecturer) {
                    $text .= "\n$lecturer->position $lecturer->firstname $lecturer->lastname $lecturer->patronymic";
                }
                $text .= $lesson->comment ? "\nКоментар викладача:\n$lesson->comment" : '';
                $text .= "\nА лекція мала бути: {$lesson->time->format('Y-m-d H:i:s')} \n\n";
            }
        return $text;
    }


}
