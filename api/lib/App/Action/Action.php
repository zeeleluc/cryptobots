<?php
namespace App\Action;

use App\Object\BaseObject;

class Action extends BaseObject
{
    private BaseAction $action;

    public function setAction(BaseAction $action): void
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }
}
