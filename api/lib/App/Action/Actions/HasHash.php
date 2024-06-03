<?php
namespace App\Action\Actions;

use App\Action\BaseAction;

class HasHash extends BaseAction
{
    public function run(): array
    {
        $hash = $this->getRequest()->getParam('hash');
        if (!$hash) {
            return [
                'has_hash' => false,
            ];
        }

        $allHashes = $this->getAllHashes();
        return [
            'has_hash' => in_array($hash, $allHashes),
        ];
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
