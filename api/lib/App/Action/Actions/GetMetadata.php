<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Service\Metadata;

class GetMetadata extends BaseAction
{
    public function run(): array
    {
        $id = $this->getRequest()->getParam('id');
        if (!is_numeric($id)) {
            return [
                'error' => 'Not found.',
            ];
        }

        return (new Metadata())->get($id);
    }
}
