<?php
/**
 * Core bootloader
 *
 * @author Serhii Shkrabak
 */

/* RESULT STORAGE */
$RESULT = [
    'state' => 0,
    'data' => []
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
    file_put_contents('strLog.txt', $str. "\n\n", FILE_APPEND);
}

$CORE = new \AIRController\Main;
$RESULT['data'] = $CORE->exec();
