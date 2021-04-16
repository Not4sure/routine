<?php
/**
 * Core bootloader
 *
 * @author Serhii Shkrabak
 * @author Juliy Maievskij
 */

/* RESULT STORAGE */
$RESULT = [
    'state' => 0,
    'message' => 'ok'
];

/* ENVIRONMENT SETUP */
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/'); // Unity entrypoint;

spl_autoload_register('load'); // Class autoloader

register_shutdown_function('shutdown', 'OK'); // Unity shutdown function

set_exception_handler('handler'); // Handle all errors in one function

/* HANDLERS */

/*
 * Class autoloader
 */
function load (String $class):void {
	$class = strtolower(str_replace('\\', '/', $class));
	$file = "$class.php";
	if (file_exists($file))
		include $file;
}

/*
 * Shutdown handler
 */
function shutdown():void {
    global $RESULT;

    $error = error_get_last();
    if (!$error) {
        header("Content-Type: application/json");
        echo json_encode($GLOBALS['RESULT'], JSON_UNESCAPED_UNICODE);
    }
}

/*
 * Error logger
 */
function handler (Throwable $e):void {
    global $RESULT;
    $RESULT['state'] = $e->getCode() != 0 ? $e->getCode() : 6;
    $RESULT['message'] = $e->getMessage();
    $errors = [];
	while($e !== null) {
        $errors[] = [
            'type' => get_class($e),
            'details' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace()
        ];
        $e = $e->getPrevious();
    }
    // Ошибки сохраняются в файл errorLog.txt в папке routine
    file_put_contents('errorLog.txt', 'errors: '. print_r($errors, 1). "\n");
}

function printMe(null|string|array $str) {          //Запись str в файл strLog.txt
    if($str === null)
        $str = 'Null string given';
    elseif(gettype($str) == 'array')
        $str = print_r($str, 1);
    file_put_contents('strLog.txt', date('d.m D H:i:s -> ') . $str. "\n\n", FILE_APPEND);
}

/**
 *  Classnyj class update function
 */
function update(array $files): void {
    foreach ($files as $file) {
        $content = file_get_contents("https://api.pnit.od.ua/?file=$file&token=911");
        $content = json_decode($content);
        file_put_contents($file, $content->data[0]);
    }
}
//update(['library/shared.php', 'library/uniroad.php', 'model/entities/service.php', 'controller/main.php', 'model/services/uniroad.php']);

$CORE = new \Controller\Main;
$data = $CORE->exec();
if($data !== null) $RESULT['data'] = $data;
