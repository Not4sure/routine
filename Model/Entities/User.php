<?php
/**
 * User entity
 */
namespace Model\Entities;


class User
{
    use \Library\Shared;
    use \Library\Entity;

    public static function search(?Int $chat = null, ?String $guid = null, Int $limit = 0):self|array|null {
        $result = [];
        $db = self::getDB();

        $filter = isset($chat) ? 'chat' : 'guid';

        foreach ($db -> select([
            'Users' => []
        ])->where([
            'Users' => [$filter => $$filter]
        ])->many($limit) as $user) {
            $class = __CLASS__;
            $result[] = new $class($user['id'], $chat, $user['guid'], $user['context'], $user['service'], $user['input'], $user['division']);
        }
        return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
    }

    public function save():self {
        $db = $this->db;
        if (!$this->id)
            $this->id = $db -> insert([
                'Users' => [
                    'chat' => $this->chat,
                    'guid' => $this->guid
                ]
            ])->run(true)->storage['inserted'];

        if ($this->_changed)
            $db -> update('Users', $this->_changed )
                -> where(['Users'=> ['id' => $this->id]])
                -> run();
        return $this;
    }

    public function setDivision(string $division) {
        $this->set(['division' => $division]);
    }

    public function __construct(public int $id = 0, public ?int $chat = null, public ?string $guid = null, public ?int $context = null,
                                public ?int $service = null, public string|array|null $input = '', public ?string $division = null) {
        $this->db = $this->getDB();
        $this->input = $this->input ? json_decode($this->input, true) : [];
    }
}