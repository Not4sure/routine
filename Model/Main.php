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

class Main {
	use \Library\Shared;
    use \Library\Uniroad;

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
        $user = Entities\User::search(guid: $this->getVar('user'), limit: 1);
        if(!isset($user)) $user = new Entities\User(guid: $this->getVar('user'));

        if($value == '/start'){
            $user->set(['context' => 1]);
        }

        $context = Entities\Message::search(id: $user->context, limit: 1);

        if($type == 'message') {
            if($context->type == 2) {
                $user->{'set'. $context->code}($value);
                $context = Entities\Message::search(id: $context->parent, limit: 1);
            }
        } elseif($type == 'click') {
            if($code == 12345) { // Кнопка "назад"
                if($user->context == 1) {
                    $result = ['type' => 'context', 'set' => null];
                } else {
                    $context = Entities\Message::search(id: $context->parent, limit: 1);
                }
            } else {
                $message = Entities\Message::search(id: $code, parent: $user->context, limit: 1);
                if(isset($message))
                    switch($message->type) {
                        case 0:
                            $this->uni()->get('proxy', [
                                'type' => $message,
                                'value' => (new Entities\Routine($user->division))->getText(),
                                'to' => $user->guid,
                            ], 'uni/push')->one();
                            break;
                        case 1:
                        case 2:
                            $context = $message;
                            break;
                    }
            }
        } else
            throw new \Exception('А какого, собственно, тип неправильный?', 5);

        if(!isset($result))
            $result = [
                'type' => 'message',
                'value' => $context->text,
                'to' => $user->guid,
                'keyboard' => [
                    'inline' => true,
                    'buttons' => $context->getKeyboard(uni: true, columns: 2)
                ]
            ];

        $user->set(['context' => $context->id]);
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