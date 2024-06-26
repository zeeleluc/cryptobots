<?php
namespace App\Action\Actions;

use App\Action\BaseAction;

class GetAllHashes extends BaseAction
{
    public function run(): array
    {
        return $this->getAllHashes();
    }

    private function getAllHashes()
    {
        if ($hashes = $this->getSession()->getItem('all-hashes')) {
            return $hashes;
        }

        $allHashes = $this->getTokenHashQuery()->getAllHashes();
        $this->getSession()->setSession('all-hashes', $allHashes);
    }
}
