<?php declare(strict_types=1);

namespace SeStep\Executives\Components;

use Nette\Application\UI;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;
use SeStep\Executives\Model\Entity\Condition;

class ConditionsList extends UI\Component
{
    /** @var Condition[] */
    private $conditions;

    /**
     * @param Condition[] $conditions
     */
    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function render()
    {
        echo $this->getHtml();
    }

    public function getHtml(): Html
    {
        if (empty($this->conditions)) {
            return Html::el('div', '-');
        }
        $list = Html::el('ul');
        foreach ($this->conditions as $condition) {
            $listItem = Html::el('li');
            $listItem->addHtml($this->getConditionElement($condition));
            $list->addHtml($listItem);
        }

        $conditionsDiv = Html::el('div', ['class' => 'action-conditions']);
        $conditionsDiv->addText('Pokud:');
        $conditionsDiv->addHtml($list);

        return $conditionsDiv;
    }

    private function getConditionElement(Condition $condition)
    {
        switch ($condition->type) {
            case 'th.answerEquals':
                $text = Html::el('span', $condition->type . ' ');
                $value = Html::el('span', $condition->params);
                $value->class[] = 'value';

                $el = Html::el('div');
                $el->addHtml($text);
                $el->addHtml($value);
                return $el;
        }

        throw new InvalidArgumentException("Condition type $condition->type not recognized");
    }
}
