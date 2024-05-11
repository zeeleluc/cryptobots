<?php
namespace App\Action\Actions;

use App\Action\Actions\Cli\Migrate;
use App\Action\BaseAction;

class Cli extends BaseAction
{

    public function run(): array
    {

        if (!$_SERVER['argv']) {
            exit;
        }

        if (!isset($_SERVER['argv'][1])) {
            exit;
        }

        $action = $_SERVER['argv'][1];

        if ($action === 'migrate') {
            (new Migrate())->run();
        }

        return [
            'Done.',
        ];
    }
}
