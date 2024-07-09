<?php
namespace App\Service;

class Metadata
{
    public function get(int $id): array
    {
        $metadata = file_get_contents(env('METADATA_URL') . $id . '.json');
        if (!$metadata) {
            return [
                'error' => 'Not found.',
            ];
        }

        return (array) json_decode($metadata, true);
    }
}
