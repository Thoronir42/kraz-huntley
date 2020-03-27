<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components;

use CP\TreasureHunt\Model\Entity\Action;
use Nette\Application\UI;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;

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
            case Action::TYPE_ACTIVATE_CHALLENGE:
                $text = Html::el('span', 'Aktivovat výzvu ');
                $value = Html::el('span', $this->action->params);
                $value->class[] = 'value';
                $el = Html::el('div');
                $el->addHtml($text);
                $el->addHtml($value);

                return $el;

            case Action::TYPE_REVEAL_NARRATIVE:
                $text = Html::el('span', 'Zobrazit průpravu ');
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
