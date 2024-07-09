<?php
namespace App;

class Api
{
    private string $apiRoot;

    public function __construct()
    {
        $this->apiRoot = str_replace('view', 'api', ROOT);

        include($this->apiRoot . '/lib/App/Service/Metadata.php');
    }

    public function getMetadata(int $id)
    {
        return (new \App\Service\Metadata())->get($id);
    }
}
