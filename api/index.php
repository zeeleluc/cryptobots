<?php
include_once 'preload.php';
include_once 'autoloader.php';
include_once 'vendor/autoload.php';
include_once 'utilities.php';

header("Access-Control-Allow-Origin: " . env('APP_ORIGIN_URL'));
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if (!is_cli()) {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

try {
    $init = new \App\Initialize();
    $init->action()->run();
} catch (Exception $e) {
    ob_start();
    $error_page = ob_get_contents();
    ob_end_clean();

    echo $error_page;
}
