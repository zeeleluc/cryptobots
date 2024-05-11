<?php
namespace App\Action\Actions;

use App\Action\BaseAction;

class HomeAction extends BaseAction
{
    public function run(): array
    {
        return [
            'Welcome to this endpoint.',
        ];
    }
}
