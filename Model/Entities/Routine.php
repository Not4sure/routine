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
    private ?array $lessons;

    /**
     * Routine constructor.
     * @param string $division
     * @param \DateTime|null $time
     * @param string $type
     */
    public function __construct(string $division, private ?\DateTime $time = null, string $type = 'day') {
        if($type = 'day' && !$time) $this->time = new \DateTime('today');
//        $this->day = (int)date('N', $this->time);
//        $this->week = (($this->time - FIRST_DAY) / (7 * 24 * 60 * 60)) % 2 ? 'p' : 'u';
        $this->division = $division;
        $this->type = $type;
        $this->getLessons();
    }

    private function getLessons() {
        $this->lessons = Lesson::search(division: $this->division, since: $this->time->getTimestamp(),
            till: strtotime('tomorrow', $this->time->getTimestamp()));
    }

    public function getText() {
        $text = "Розклад на {$this->time->format('d F')} ";
        $text .= "для групи $this->division\n\n";
        if(!empty($this->lessons))
            foreach($this->lessons as $lesson) {
                $text .= match($lesson->type) {
                    'lecture' => 'лек. ',
                    'practice' => 'пр. ',
                    'lab' => 'лаб. ',
                };
                $text .= "{$lesson->subject->name}\n";
                $text .= $lesson->room ? "Аудиторія {$lesson->room->name}\n" : "\n";

                foreach($lesson->lecturers as $lecturer) {
                    $text .= $lecturer ? "\n$lecturer->position $lecturer->firstname $lecturer->lastname $lecturer->patronymic" : 'Error blyat';
                }
                $text .= $lesson->comment ? "\nКоментар викладача:\n$lesson->comment" : '';
                $text .= "\nА лекція мала бути: {$lesson->time->format('Y-m-d H:i:s')} \n\n";
            }
        else
            $text .= 'А тут нічого немає. Так буває.';
        return $text;
    }


}
