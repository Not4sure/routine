<?php
/**
 * AIR routine bot
 *
 * @author Yulij Maievskij
 * @global object $CORE
 * @package Controller\Main
 */
namespace AIRController;
class Main
{
	use \Library\Shared;
	private \Model\Main $model;
	private array $reply = [
	    'text' => '',
        'keyboard' => null
    ];

    public function exec():?array {
        $result = null;
        $url = $this->getVar('REQUEST_URI', 'e');
        $url = explode('?', $url)[0];
        $path = explode('/', $url);

//        if (isset($path[2]) && !strpos($path[1], '.')) { // Disallow directory changing
//            $file = ROOT . 'model/config/methods/' . $path[1] . '.php';
//            if (file_exists($file)) {
//                include $file;
//                if (isset($methods[$path[2]])) {
//                    $details = $methods[$path[2]];
//                    $request = [];
//                    foreach ($details['params'] as $param) {
//                        $var = $this->getVar($param['name'], $param['source']);
//                        if ($var) {
//                            $request[$param['name']] = $var;
//                        } else {
//                            if (!isset($param['required'])) {
//
//                            }
//                            else
//                                throw new \Exception($param['name'], 1);
//                        }
//                    }
//                    if (method_exists($this->model, $path[1] . $path[2])) {
//                        $method = [$this->model, $path[1] . $path[2]];
//                        $result = $method($request);
//                    }
//                }
//            }
//        }

        if (isset($path[2]) && !strpos($path[1], '.')) { // Disallow directory changing
            $file = ROOT . 'model/config/methods/' . $path[1] . '.php';
            if (file_exists($file)) {
                include $file;
                if (isset($methods[$path[2]])) {
                    $details = $methods[$path[2]];
                    $prevError = null;
                    $error = null;
                    $request = [];
                    foreach ($details['params'] as $param) {
                        $var = $this->getVar($param['name'], $param['source']);
                        printMe($param['name']. ' ' .$var);
                        if($var == '') {
                            if($param['required']){
                                $error = new \Exception($param['name'], 1, $prevError);
                                $prevError = $error;
                            }
                        }
                        if($var !== null)
                            $request[$param['name']] = $var;
                    }

                    if($error !== null) throw $error;

                    if (method_exists($this->model, $path[1] . $path[2])) {
                        $method = [$this->model, $path[1] . $path[2]];
                        $result = $method($request);
                    }
                }
            }
        }

        return $result;
    }


	public function __construct() {
        $this->model = new \Model\Main();
	}
}