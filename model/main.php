<?php
/**
 * ONPU routine bot
 *
 * @author Serhii Shkrabak
 * @author Juliy Maievskij
 * @global object $CORE->model
 * @package Model\Main
 */
namespace Model;
use \Library\Uniroad;
//use \Services\Uniroad;

class Main
{
	use \Library\Shared;

	private \Model\Services\Telegram $TG;

	public function tgwebhook(string $token, string $input): ?array {
        if($token == $this->getVar('TG_TOKEN', 'e')) {
            $input = json_decode($input, true);

            if(isset($input['callback_query'])){
                $this->TG->process($input['callback_query']['message']);
            } else
                if(isset($input['edited_message']))
                    $this->TG->process($input['edited_message'], edited: true);
                else
                    if(isset($input['message']))
                        $this->TG->process($input['message']);
                    else
                        $this->TG->allert($input);
        } else
            throw new \Exception('TOKEN blyat', 3);
        return null;
    }

    public function uniwebhook(String $type = '', String $value = '', Int $code = 0):?array {
        $result = null;
        printMe(['type' => $type, 'value' => $value]);
        switch ($type) {
            case 'message':
                if ($value == 'вийти') {
                    $result = ['type' => 'context', 'set' => null];
                } elseif ($value == '/start')
                    $result = [
                        'type' => 'message',
                        'value' => 'Розклад буде',
                        'to' => $this->getVar('user'),
                        'keyboard' => [
                            'inline' => true,
                            'buttons' => \Model\Entities\Message::search(entrypoint: $value, limit: 1)->getKeyboard(uni: true, columns: 2)
                        ]
                    ];
                break;
            case 'click':
                $result = [
                    'type' => 'message',
                    'value' => "Сервіс Розклад. Натиснуто кнопку $code",
                    'user' => $this->getVar('user'),
                    'keyboard' => [
                        'inline' => false,
                        'buttons' => [
                            [['id' => 9, 'title' => 'Надати номер', 'request' => 'contact']]
                        ]
                    ]
                ];
                if($code == 16)
                    $this->uni()->get('proxy', [
                        'firstname' => 'Doctor Who',
                        'secondname' => 'Corporation',
                        'phone' => '380665413986'
                    ], 'form/submitAmbassador')->one();
                break;
        }

        return $result;
    }

    public function rotineget(string $user): array{
	    return ['Тут буде розклад на тиждень'];
    }

	public function __construct() {
        $this->db = new \Library\MySQL('core',
            \Library\MySQL::connect(
                $this->getVar('DB_HOST', 'e'),
                $this->getVar('DB_USER', 'e'),
                $this->getVar('DB_PASS', 'e')
            ) );
		$this->setDB($this->db);
        $this -> TG = new Services\Telegram(key: $this->getVar('TG_TOKEN', 'e'), emergency: 165091981);
	}
}