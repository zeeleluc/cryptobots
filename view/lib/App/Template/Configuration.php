<?php
namespace App\Template;

use App\Object\BaseObject;

abstract class Configuration extends BaseObject
{
    public function layouts()
    {
        return [
            'default',
            'error',
        ];
    }

    public function views()
    {
        return [
            'website' => [
                'home',
                'token',
            ],
        ];
    }

}
