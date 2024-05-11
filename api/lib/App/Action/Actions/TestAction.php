<?php
namespace App\Action\Actions;

use App\Action\BaseAction;

class TestAction extends BaseAction
{
    public function run(): array
    {
        return [
            'Test endpoint.',
        ];
    }
}
