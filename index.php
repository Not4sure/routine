<?php
/**
 * Core bootloader
 *
 * @author Yellow
 * @author Kostiantyn Faiuk
 * @author Juliy Maievskij
 */

/* RESULT STORAGE */
$RESULT = [
    'state' => 0,
    'message' => 'ok'
];

// <editor-fold defaultstate="collapsed" desc="Environment setup">
/* ENVIRONMENT SETUP */
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/'); // Unity entrypoint;

spl_autoload_register('load'); // Class autoloader

register_shutdown_function('shutdown', 'OK'); // Unity shutdown function

set_exception_handler('handler'); // Handle all errors in one function
// </editor-fold>

//<editor-fold desc="Handlers">
/* HANDLERS */

/*
 * Class autoloader
 */
function load (String $class):void {
	$class = str_replace('\\', '/', $class);
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

    $errors = print_r($errors, 1);
    // Ошибки сохраняются в файл errorLog.txt в папке routine
    file_put_contents('errorLog.txt', 'errors: '. $errors . "\n");

//    (new Model\Services\Telegram($_SERVER['TG_TOKEN'], emergency: 165091981))->alert('errors: '. $errors);
}
//</editor-fold>

function printMe(null|string|array $str, bool $tg = false) {          //Запись str в файл strLog.txt
    if($str === null)
        $str = 'Null string given';
    elseif(gettype($str) == 'array')
        $str = print_r($str, 1);

    if($tg)
        (new Model\Services\Telegram($_SERVER['TG_TOKEN'], emergency: 165091981))->alert($str);
    else
        file_put_contents('strLog.txt', date('d.m D H:i:s -> ') . $str. "\n\n", FILE_APPEND);
}

// function update(): void {
//    foreach ($files as $file) {
//        $content = file_get_contents("https://api.pnit.od.ua/?file=$file&token=911");
//        $content = json_decode($content);
//        printMe($content->data);
//        file_put_contents($file, $content->data[0]);
//    }

//    $content = file_get_contents("https://api.pnit.od.ua/?file=sevices/&token=911");
//    $content = json_decode($content);
//    file_put_contents($file, $content->data[0]);
// }
// update();

$CORE = new \Controller\Main;
$data = $CORE->exec();
if($data !== null) $RESULT['data'] = $data;
