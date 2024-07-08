<?php
namespace App;

use App\Action\Action as AbstractAction;
use App\Action\Actions\GetAllHashes;
use App\Action\Actions\GetBalance;
use App\Action\Actions\HasHash;
use App\Action\Actions\HomeAction;
use App\Action\Actions\MintNFT;
use App\Action\Actions\PushWalletAddress;
use App\Action\Actions\SetBalance;
use App\Action\Actions\UploadMetadata;
use App\Action\BaseAction;
use App\Object\BaseObject;
use App\Object\ObjectManager;
use App\Query\TokenHashQuery;
use App\Query\WalletQuery;

class Initialize extends BaseObject
{
    public function __construct()
    {
        ObjectManager::set(new Request());
        ObjectManager::set(new Session());
        ObjectManager::set(new AbstractAction());

        ObjectManager::set(new WalletQuery());
        ObjectManager::set(new TokenHashQuery());
    }

    public function action(): Initialize
    {
        $this->getAbstractAction()->setAction($this->resolveAction());
        $this->getAbstractAction()->getAction()->run();

        return $this;
    }

    public function run()
    {
        exit(json_encode($this->getAbstractAction()->getAction()->run()));
    }

    /**
     * @return BaseAction
     */
    private function resolveAction(): BaseAction
    {
        $get = $this->getRequest()->get();

        if (is_cli()) {
            (new Action\Actions\Cli())->run();
            exit;
        }

        if (false === isset($get['action']) || (true === isset($get['action']) && '' === $get['action'])) {
            return new HomeAction();
        } elseif ($get['action'] === 'push-wallet-address') {
            return new PushWalletAddress();
        } elseif ($get['action'] === 'set-balance') {
            return new SetBalance();
        } elseif ($get['action'] === 'get-balance') {
            return new GetBalance();
        } elseif ($get['action'] === 'has-hash') {
            return new HasHash();
        } elseif ($get['action'] === 'get-all-hashes') {
            return new GetAllHashes();
        } elseif ($get['action'] === 'upload-metadata') {
            return new UploadMetadata();
        } elseif ($get['action'] === 'mint-nft') {
            return new MintNFT();
        }

        exit(json_encode(['error' => 'Action not found.']));
    }
}
