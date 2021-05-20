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

use Model\Entities\Division;

class Main {
	use \Library\Shared;
    use \Library\Uniroad;

	private \Model\Services\Telegram $TG;
    private ?Entities\User $user;

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

    public function uniwebhook(string $type = '', string $value = '', int $code = 0):?array {
        $this->user = Entities\User::search(guid: $this->getVar('user'), limit: 1);
        if(!isset($this->user)) $this->user = new Entities\User(guid: $this->getVar('user'));

        if($value == '/start'){
            $this->user->set(['context' => 1]);
        }

        $context = Entities\Message::search(id: $this->user->context, limit: 1);

        if($type == 'message') {
            if($context->type == 2) {
                try {
                    $info = $this->{$context->code}($value);
                    $context = Entities\Message::search(id: $context->parent, limit: 1);
                } catch (\Exception $e)  {
                    $info = $e->getMessage();
                }
            }
        } elseif($type == 'click') {
            if($code == 12345) { // Кнопка "назад"
                if($this->user->context == 1) {
                    $interface = ['type' => 'context', 'set' => null];
                } else {
                    $context = Entities\Message::search(id: $context->parent, limit: 1);
                }
            } else {
                $message = Entities\Message::search(id: $code, parent: $this->user->context, limit: 1);
                if(isset($message))
                    switch($message->type) {
                        case 0:
                            $info = $this->{$message->code}();
                            break;
                        case 1:
                        case 2:
                            $context = $message;
                            break;
                    }
            }
        } else
            throw new \Exception('А какого, собственно, тип неправильный?', 5);

        // Отправка сообщения-интерфейса
        if(!isset($interface))
            $interface = [
                'type' => 'message',
                'value' => $context->text,
                'to' => $this->user->guid,
                'keyboard' => [
                    'inline' => true,
                    'buttons' => $context->getKeyboard(uni: true, columns: 2)
                ]
            ];

        // Отправка сообщения с информацией
        if(isset($info))
            $this->uni()->get('proxy', [
                'type' => 'message',
                'value' => $info,
                'to' => $this->user->guid,
            ], 'uni/push')->one();

        $this->user->set(['context' => $context->id]);
        $this->user->save();
        return $interface;
    }

    private function division(string $value):string {
        $value = mb_strtoupper($value);

        if(in_array($value, Division::getDivisions())){
            $this->user->setDivision($value);
        } else
            throw new \Exception('Немає такої групи');
        return "Обрано группу $value";
    }

    private function today():string {
        return (new Entities\Routine($this->user->division))->getText();
    }

    private function tomorrow():string {
        return (new Entities\Routine($this->user->division, time: new \DateTime('tomorrow')))->getText();
    }

    private function full():string {
        return 'Цю штуку ще не завезли)';
    }

    public function rotineget(string $user):array{
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