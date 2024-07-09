<?php

/**
 * return null|string
 */
if (!function_exists('env')) {
    function env(string $key)
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(str_replace('/view', '', __DIR__));
        $dotenv->load();

        if (!array_key_exists($key, $_ENV)) {
            return null;
        }

        return $_ENV[$key];
    }
}

if (!function_exists('is_local')) {
    function is_local(): bool
    {
        return env('ENV') === 'local';
    }
}
