<?php
include_once 'preload.php';
include_once 'autoloader.php';
include_once 'vendor/autoload.php';
include_once 'utilities.php';

try {
    $init = new \App\Initialize();
    $init->action()->run();
} catch (Exception $e) {
    ob_start();
    $error_page = ob_get_contents();
    ob_end_clean();

    echo $error_page;
}
