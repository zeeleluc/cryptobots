<?php
namespace App;

class Request
{
    private array $get;

    public function __construct()
    {
        $this->get = $_GET;
    }

    public function get(): array
    {
        return $this->get;
    }

    public function isGet(): bool
    {
        return ('get' === strtolower($_SERVER['REQUEST_METHOD']));
    }

    public function getParam($param): ?string
    {
        if (isset($this->get[$param])) {
            return $this->get[$param];
        }

        return null;
    }
}
