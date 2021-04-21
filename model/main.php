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
        printMe(['type' => $type, 'value' => $value, 'code' => $code]);

        $user = \Model\Entities\User::search(guid: $this->getVar('user'), limit: 1);
        if(!isset($user)) $user = new \Model\Entities\User(guid: $this->getVar('user'));

        if($type == 'message') {
            switch($value) {
                case 'вийти':
                    $result = ['type' => 'context', 'set' => null];
                    break;
                case '/start':
                    $user->set(['context' => 1]);
                default:
                    //И чЁ?
            }
        } elseif($type == 'click') {
            if($code == 12345) { // Кнопка "назад"
                if($user->context == 1) {
                    $result = ['type' => 'context', 'set' => null];
                } else {
                    // меняем контекст
                }
            } else {
                $message = \Model\Entities\Message::search(id: $code);
                switch($message->type) {
                    case 0:
                        // message
                        break;
                    case 1:
                        $user->set(['context' => $message->id]);
                        break;
                    case 2:
                        // question
                        break;
                }
            }
        } else
            throw new \Exception('А какого, собственно, тип неправильный?', 5);

        if(!isset($result))
            $result = [
                'type' => 'message',
                'value' => 'Розклад буде',
                'to' => $user->guid,
                'keyboard' => [
                    'inline' => true,
                    'buttons' => \Model\Entities\Message::search(id: $user->context, limit: 1)->getKeyboard(uni: true, columns: 2)
                ]
            ];

//        switch ($type) {
//            case 'message':
//                if ($value == 'вийти') {
//                    $result = ['type' => 'context', 'set' => null];
//                } elseif ($value == '/start') {
//                    $user->set(['context' => 1]);
//                    $result = [
//                        'type' => 'message',
//                        'value' => 'Розклад буде',
//                        'to' => $user->guid,
//                        'keyboard' => [
//                            'inline' => true,
//                            'buttons' => \Model\Entities\Message::search(entrypoint: $value, limit: 1)->getKeyboard(uni: true, columns: 3)
//                        ]
//                    ];
//                }
//                break;
//            case 'click':
//                if($code == 12345 && $user->context == null) {
//                    $result = ['type' => 'context', 'set' => null];
//                    break;
//                }
//                $message = \Model\Entities\Message::search(id: $code);
//                $result = [
//                    'type' => 'message',
//                    'to' => $user->guid,
//                    'keyboard' => [
//                        'inline' => true,
//                        'buttons' => \Model\Entities\Message::search(entrypoint: $value, limit: 1)->getKeyboard(uni: true, columns: 2)
//                    ]
//                ];
//                if($code == 14) {
//                    $result['value'] = (new \Model\Entities\Routine('УП-191'))->getText();
//                } elseif($code == 20) {
//                    $result['value'] = (new \Model\Entities\Routine('УП-191', time: strtotime('+1 day')))->getText();
//                } else {
//                    $result['value'] = "Сервіс Розклад. Натиснуто кнопку $code";
//                }
//                break;
//        }
//
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