<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Slack;

class SetBalance extends BaseAction
{
    public function run(): array
    {
        $address = $this->getRequest()->getPostParam('walletAddress');
        $sessionId = $this->getRequest()->getPostParam('sessionId');
        $balance = (float) $this->getRequest()->getPostParam('balance');

        if ($address && $sessionId) {
            $wallet = $this->getWalletQuery()->getWalletByIdentifierAndAddress($sessionId, $address);
            $wallet->balance = $balance;
            $wallet->save();

            return ['Wallet balance updated.'];
        } else {
            return ['Wallet balance NOT updated.'];
        }
    }
}
