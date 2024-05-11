<?php
namespace App\Action;

use App\Object\BaseObject;

abstract class BaseAction extends BaseObject
{
    abstract public function run(): array;
}
