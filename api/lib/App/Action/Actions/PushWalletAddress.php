<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Model\Wallet;
use App\Slack;

class PushWalletAddress extends BaseAction
{
    public function run(): array
    {
        $address = $this->getRequest()->getPostParam('walletAddress');
        $sessionId = $this->getRequest()->getPostParam('sessionId');

        if ($address && $sessionId) {
            $wallet = new Wallet();
            $wallet->initNew([
                'identifier' => $sessionId,
                'address' => $address,
            ]);

            return ['Wallet address stored.'];
        }

        return ['Wallet address NOT stored.'];
    }
}
