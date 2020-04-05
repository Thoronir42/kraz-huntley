<?php declare(strict_types=1);

namespace SeStep\Executives\Components;

use Nette\Application\UI;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;
use SeStep\Executives\Model\Entity\Action;

class ActionView extends UI\Component
{
    /** @var Action */
    private $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function getHtml()
    {
        switch ($this->action->type) {
            case 'th.activateChallenge':
                $text = Html::el('span', $this->action->type);
                $value = Html::el('span', $this->action->params);
                $value->class[] = 'value';
                $el = Html::el('div');
                $el->addHtml($text);
                $el->addHtml($value);

                return $el;

            case 'th.revealNarrative':
                $text = Html::el('span', $this->action->type);
                $value = Html::el('span', $this->action->params);
                $value->class[] = 'value';
                $el = Html::el('div');
                $el->addHtml($text);
                $el->addHtml($value);

                return $el;
        }

        throw new InvalidArgumentException("Action type {$this->action->type} not recognized");
    }

    public function createComponentConditions()
    {
        return new ConditionsList($this->action->conditions);
    }
}
