<?php

include_once 'preload.php';
include_once 'autoloader.php';
include_once 'vendor/autoload.php';
include_once 'utilities.php';

if (env('ENV') === 'local') {
    error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING);
    ini_set('display_errors', 'On');
} else {
    error_reporting(0);
    ini_set('display_errors', 'Off');
}

try {
    $init = new \App\Initialize();
    $init->action()->show();
} catch (Exception $e) {
    ob_start();
    $error_page = ob_get_contents();
    ob_end_clean();

    $slack = new \App\Slack();
    $slack->sendErrorMessage($e->getMessage());

    echo $error_page;
}
