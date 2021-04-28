<?php
/**
 * Message entity
 *
 * @author Serhii Shkrabak
 * @global object $CORE->model
 * @package Model\Entities\Message
 */
namespace Model\Entities;

use http\Params;

class Message
{
	use \Library\Shared;
	use \Library\Entity;

	public static function search(Int $id = 0, ?Int $parent = 0, Int $type = 0, ?String $guid = null,
		?String $code = null, ?String $title = null, ?String $text = null,
		?String $entrypoint = null, Int $position = 0, ?Int $service = null,
		?Bool $reload = null, Int $limit = 0):self|array|null {
		$result = [];
		$db = self::getDB();
		$messages = $db -> select(['Messages' => []]);

		foreach (['id', 'entrypoint', 'parent'] as $var)
			if ($$var)
				$filters[$var] = $$var;
		if(!empty($filters))
			$messages->where(['Messages'=> $filters]);

		foreach ($messages->many($limit) as $message) {
			$class = __CLASS__;
			$result[] = new $class($message['id'], $message['parent'], $message['type'], $message['guid'],
				$message['code'], $message['title'], $message['text'], $message['entrypoint'],
				$message['position'], $message['service'], $message['reload']);
		}
		return $limit == 1 ? (isset($result[0]) ? $result[0] : null) : $result;
	}

	public function save():self {
		$db = $this->db;
		if(!$this->id) {}
		return $this;
	}

	public function getChildren($limit = 0):array|self {
		return $this::search(parent: $this->id, limit: $limit);
	}

	public function getKeyboard(bool $uni = false, int $columns = 0):array {
        $titleIndex = $uni ? 'title' : 'text';
		$buttons = [];
        $row = 0;
        $column = 0;
		foreach ( $this->getChildren() as $button) {
            if($button->title) {
                $buttons[$row][$column] = [
                    $titleIndex => $button->title,
                    'id' => $uni ? $button->id : null
                ];
                $column++;
                if($column >= $columns && $columns > 0) {
                    $column = 0;
                    $row++;
                }
            }
		}
		if($uni) $buttons[++$row] = [[
		    $titleIndex => 'Повернутися',
            'id' => 12345
        ]];


		return $buttons ?  $buttons : [];
	}

	public function __construct(public int $id = 0, public ?int $parent = 0, public int $type = 0, public ?string $guid = null,
								public ?string $code = null, public ?string $title = null, public ?string $text = null,
								public ?string $entrypoint = null, public int $position = 0, public ?int $service = null,
								public ?bool $reload = null) {
		$this->db = $this->getDB();
	}
}