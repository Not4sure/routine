<?php
/**
 * ONPU routine bot
 *
 * @author Serhii Shkrabak
 * @author Juliy Maievskij
 * @global object $CORE
 * @package Controller\Main
 */
namespace Controller;
class Main
{
	use \Library\Shared;
	private \Model\Main $model;

    public function exec():?array {
        $result = null;
        $url = $this->getVar('REQUEST_URI', 'e');
        $url = explode('?', $url)[0];
        $path = explode('/', $url);

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
        // CORS configuration
        $front = $this -> getVar('FRONT', 'e');

        foreach ( [$front] as $allowed )
            if ( ORIGIN == "https://$allowed") {
                header('Access-Control-Allow-Origin: '. ORIGIN);
                header('Access-Control-Allow-Credentials: true');
            }
        $this->model = new \Model\Main;
	}
}