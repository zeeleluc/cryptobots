<?php
namespace App\Action;

use App\Auth;
use App\Object\BaseObject;
use App\Template\Template;
use App\Variable;

abstract class BaseAction extends BaseObject
{
    protected bool $terminal = false;

    private array $variables = [];

    private Template $template;

    public function __construct()
    {
        $htmlTitle = 'CryptoBots Viewer';

        $this->setVariable(new Variable('htmlTitle', $htmlTitle));
        $this->template = new Template();
    }

    public function run()
    {
        if ($this->terminal) {
            exit;
        }

        if (array_key_exists('action', $this->getRequest()->get())) {
            $this->setVariable(new Variable('currentAction', $this->getRequest()->get()['action']));
        } else {
            $this->setVariable(new Variable('currentAction', null));
        }

        return $this;
    }

    public function setVariable($variable)
    {
        $this->variables[$variable->getName()] = $variable;
    }

    public function getVariables()
    {
        $variables = [];
        foreach ($this->variables as $variable) { /* @var $variable Variable */
            $variables[$variable->getName()] = $variable->getValue();
        }
        return $variables;
    }

    public function getVariable($variable_name)
    {
        if (false === array_key_exists($variable_name, $this->getVariables())) {
            throw new \Exception(sprintf('Variable %s does not exists.', $variable_name));
        }

        return $this->getVariables()[$variable_name];
    }

    public function setLayout($layout_name)
    {
        $this->getTemplate()->setLayout($layout_name);
    }

    public function setView($view_name)
    {
        $this->getTemplate()->setView($view_name);
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTerminal(bool $terminal): void
    {
        $this->getTemplate()->setTerminal($terminal);
    }
}
