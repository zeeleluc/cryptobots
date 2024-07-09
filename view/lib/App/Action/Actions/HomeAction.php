<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Api;
use App\Model\Token;
use App\Variable;

class HomeAction extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/home');
    }

    public function run()
    {
        parent::run();

        $ids = range(0, 1200);
        shuffle($ids);
        $ids = array_slice($ids, 0, 4);

        $api = new Api();
        $tokens = [];
        foreach ($ids as $id) {
            $metadata = $api->getMetadata($id);
            $tokens[] = new Token($metadata);
        }

        $this->setVariable(new Variable('tokens', $tokens));

        return $this;
    }
}
