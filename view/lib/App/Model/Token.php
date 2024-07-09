<?php
namespace App\Model;

class Token
{

    public int $id;

    public string $name;

    public array $metadata;

    public array $attributes;

    public string $imageV1;

    public string $imageV2;

    public string $viewUrl;

    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
        $this->name = $this->metadata['name'];
        $this->attributes = $this->metadata['attributes'];
        $this->id = str_replace('BOT #', '', $this->name);

        $this->imageV1 = env('V1_URL') . str_replace('/', '', $metadata['image']);
        $this->imageV2 = env('V2_URL') . $this->id . '.jpg';

        $this->viewUrl = env('APP_VIEW_URL') . '/token/' . $this->id;
    }
}
