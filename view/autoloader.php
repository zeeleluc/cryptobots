<?php

define('LIBRARY_PATH', realpath(dirname(__FILE__)) . DS . 'lib' . DS);

set_include_path(
    get_include_path() . PATH_SEPARATOR . LIBRARY_PATH
);

spl_autoload_register(function ($class_name) {
    if (file_exists(LIBRARY_PATH . str_replace('\\', '/', $class_name) . '.php')) {
        include LIBRARY_PATH . str_replace('\\', '/', $class_name) . '.php';
    }
});
