<?php
/**
 * Telegram communication service
 *
 * @author Serhii Shkrabak
 * @author Juliy Maievskij
 * @global object $CORE->model->UNI
 * @package Model\Services
 */
namespace Model\Services;
use \Model\Entities\Routine;
class Telegram
{
    use \Library\Shared;


    private ?Int $chat;

    public function send(String $text, Int $chat = 0, ?Array $keyboard = [], Int $reload = 0) {
        if(!$chat)
            $chat = $this->chat;
        if($reload)
            $method = 'editMessageText';
        else
            $method = 'sendMessage';
        $reply = '';
        if(!empty($keyboard)) {
            $reply = '&reply_markup=';
            $reply .= json_encode( [
                'keyboard' => $keyboard,
                'one_time_keyboard' => false,
                'resize_keyboard' => true
            ]);
        } elseif($keyboard === null) {
            $reply = '&reply_markup=';
            $reply .= json_encode( [
                'remove_keyboard' => true
            ]);
        }

        $text = urlencode($text);
        file_get_contents("https://api.telegram.org/bot{$this->key}/$method?parse_mode=markdown&chat_id=$chat&text=$text" . ($reload? "&message_id=$reload" : '') . $reply );
    }

    public function alert(String $body = '') {
        $this->send($body, $this->emergency);
    }

    public function setChat(Int $id):self {
        $this->chat = $id;
        return $this;
    }

    private function getContext(\Model\Entities\User $user, String $text):string {
        $message = \Model\Entities\Message::search(id: $user->message, limit: 1);
        $fields = $message->getChildren();
        $input = $user->input;
        $response = '';
        $full = false;

        foreach ( $fields as $field ) {
            if (!isset($input[$field->code])) {
                if (!$full) {
                    $input[$field->code] = $text;
                    $full = true;
                }
                else {
                    $response = ( $field->title ? '*' . $field->title . "*\n\n" : '') . $field->text;
                    break;
                }
            }
        }

        $update = ['input' => $input];
        if (count($input) == count($fields)) {
            $update['message'] = null;
            if($message->service) {
                $service = \Model\Entities\Service::search(id: $message->service, limit: 1);
                $service = new \Model\Entities\Service($input['s-title'], $input['s-description'], user: $user->id,
                    token: $this->generateToken(32), signature: $this->generateToken(32));
                $service->save();
                $response = "✅ *ЗГЕНЕРОВАНО КЛЮЧІ*\n\nВикористовуйте для інтеграції наступні ключі:\n\n🔐 *Токен:* " . $service->token
                    . "\nТокен призначено для верифікації запитів до Вашого сервісу\n\n🖇 *Підпис:* " . $service->signature
                    . "\nПідпис призначено для виконання запитів з Вашого сервісу до Єдиних інформаційних систем\n";
            }
        }
        $user->set($update);
        return $response;
    }

    private function getReply(String $code):string {
        $db = $this->db;
        $reply = $db -> select(['Messages' => []])
            -> where(['Messages'=> ['code' => $code]])
            -> many();
        if (empty($reply))
            $reply = $this->getReply('unknown');
        else {
            $reply = $reply[mt_rand(0, count($reply)-1)];
        }
        return $reply['text'];
    }

    public function process(Array $entrypoint, String $terminal = '', Bool $edited = false) {

        if (!isset($this->chat))
            $this->setChat($entrypoint['chat']['id']);

        $user = \Model\Entities\User::search(chat: $this->chat, limit: 1);

        if (!$user) {
            $user = new \Model\Entities\User(chat: $this->chat);
            $user->save();
        }

        $text = $entrypoint['text'];
        $keyboard = [];
        $reload = 0;

        if ($user->message)
            $response = $this->getContext($user, $text);
        else {
            $message = \Model\Entities\Message::search(entrypoint: $text, limit: 1);
            if ($message) {
                switch($message->type) {
                    case 2: // вводиться форма
                        if ($entrypoint) {
                            $user->set([
                                'message' => $message->id,
                                'input' => []
                            ]);
                            $field = $message->getChildren(1);
                            $response = ( $field->title ? '*' . $field->title . "*\n\n" : '') . $field->text;
                            $keyboard = null;
                        }
                        break;
                    default:
//                        if($user->division == null && $message->entrypoint != '/start') {
//                            $response = $this->getReply('no division');
//                            break;
//                        }
                        switch ($message->title) {
                            case 'Сьогодні' :
                                $response = (new \Model\Entities\Routine('УП-191'))->getText();
                                break;
                            case 'Завтра' :
                                $response = (new \Model\Entities\Routine('УП-191', strtotime('+1 day')))->getText();
                                break;
                            default:
                                $response = ($message->title ? '*' . $message->title . "*\n\n" : '') . $message->text;
                                $keyboard = $message->getKeyboard(columns: 2);
                                break;
                        }

                }
            } else {
                $response = $this->getReply('unknown');
            }
        }

        if($response)
            $this->send($response, keyboard: $keyboard, reload: $reload);
    }

    public function __construct(private String $key, private Int $emergency) {
        $this->db = $this->getDB();
    }

}