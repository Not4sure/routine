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
    }

    private function searchLessons() {
        // Searching lessons in db or in cache
    }

    public function getText() {
        $text = "Розклад на день $this->day ";
        $text .= $this->week == 'p' ? 'парного ' : 'непарного ';
        $text .= "тижню буде тут для групи $this->division";
        $text .= "дата: " . date('d.m D H', $this->time);
        return $text;
    }


}
