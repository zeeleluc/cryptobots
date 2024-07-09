<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Api;
use App\Model\Token;
use App\Variable;

class TokenAction extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/token');
    }

    public function run()
    {
        parent::run();

        $id = $this->getRequest()->getParam('id');
        if (!is_numeric($id) || ($id < 0 || $id > 1200)) {
            header('Location: /');
            exit;
        }

        $id = (int) $id;

        $metadata = (new Api())->getMetadata($id);

        if ($id === 0) {
            $urlPrevious = null;
        } else {
            $urlPrevious = env('APP_VIEW_URL') . '/token/' . ($id - 1);
        }

        if ($id === 1200) {
            $urlNext = null;
        } else {
            $urlNext = env('APP_VIEW_URL') . '/token/' . ($id + 1);
        }

        $this->setVariable(new Variable('token', new Token($metadata)));
        $this->setVariable(new Variable('urlPrevious', $urlPrevious));
        $this->setVariable(new Variable('urlNext', $urlNext));

        return $this;
    }
}
