<?php
namespace App\Action\Actions;

use App\Action\BaseAction;

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

        $metadata = file_get_contents(env('METADATA_URL') . $id . '.json');
        if (!$metadata) {
            return [
                'error' => 'Not found.',
            ];
        }

        return (array) json_decode($metadata, true);

    }
}
