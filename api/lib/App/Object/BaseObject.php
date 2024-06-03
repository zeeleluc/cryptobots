<?php
namespace App\Object;

use App\Action\Action;
use App\Query\TokenHashQuery;
use App\Query\WalletQuery;
use App\Request;
use App\Session;

abstract class BaseObject
{
    private string $objectName;

    private ObjectManager $objectManager;

    public function setObjectManager(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    public function getRequest(): Request
    {
        return ObjectManager::getOne('App\Request');
    }

    public function getSession(): Session
    {
        return ObjectManager::getOne('App\Session');
    }

    public function getAbstractAction(): Action
    {
        return ObjectManager::getOne('App\Action\Action');
    }

    public function getWalletQuery(): WalletQuery
    {
        return ObjectManager::getOne('App\Query\WalletQuery');
    }

    public function getTokenHashQuery(): TokenHashQuery
    {
        return ObjectManager::getOne('App\Query\TokenHashQuery');
    }
}
