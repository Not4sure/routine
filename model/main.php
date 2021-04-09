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
use http\Encoding\Stream;
use Model\Services\Uni;

class Main
{
	use \Library\Shared;

	private \Model\Services\Telegram $TG;
	private \Model\Services\Uni $UNI;

	public function tgwebhook(array $data): ?array {

        if($data['token'] == $this->getVar('TG_TOKEN', 'e')) {
            $input = $data['input'];
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
                        $this->TG->allert($data['input']);
        } else
            throw new \Exception('TOKEN blyat', 3);
        return null;
    }

    public function uniwebhook(array $data): ?array {
        $query = json_decode($data['query'], 1);
        if (/*$data['token'] == $this->getVar('UNI_TOKEN', 'e')*/1) {
            foreach ($query as $request) {
                $result[] = $this->UNI->process($request);
            }
        } else
            throw new \Exception('Wrong uni token', 4);
        printMe($result);
        return $result;
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
        $this->UNI = new Services\Uni(key: $this->getVar('UNI_TOKEN', 'e'));
	}
}