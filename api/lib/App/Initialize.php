<?php
namespace App;

use App\Action\Action as AbstractAction;
use App\Action\Actions\HomeAction;
use App\Action\Actions\TestAction;
use App\Action\BaseAction;
use App\Object\BaseObject;
use App\Object\ObjectManager;

class Initialize extends BaseObject
{
    public function __construct()
    {
        ObjectManager::set(new Request());
        ObjectManager::set(new Session());
        ObjectManager::set(new AbstractAction());
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

        if (false === isset($get['action']) || (true === isset($get['action']) && '' === $get['action'])) {
            return new HomeAction();
        } elseif ($get['action'] === 'test') {
            return new TestAction();
        }

        exit(json_encode(['error' => 'Action not found.']));
    }
}
