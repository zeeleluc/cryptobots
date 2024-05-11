<?php
namespace App\Action\Actions;

use App\Action\BaseAction;

class GetBalance extends BaseAction
{
    public function run(): array
    {
        $address = $this->getRequest()->getPostParam('walletAddress');
        $sessionId = $this->getRequest()->getPostParam('sessionId');

        if ($address && $sessionId) {
            $wallet = $this->getWalletQuery()->getWalletByIdentifierAndAddress($sessionId, $address);

            return ['balance' => $wallet->balance];
        } else {
            return ['balance' => null];
        }
    }
}
