<?php declare(strict_types=1);

namespace SeStep\Executives\Components;

use Contributte\Translation\Translator;
use Nette\Application\UI;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;
use SeStep\Executives\Model\Entity\Condition;
use SeStep\Executives\Model\Service\ActionsService;

class ConditionsList extends UI\Component
{
    /** @var Condition[] */
    private $conditions;
    /** @var Translator */
    private $translator;
    /** @var ActionsService */
    private $actionsService;

    /**
     * @param Condition[] $conditions
     * @param Translator $translator
     * @param ActionsService $actionsService
     */
    public function __construct(array $conditions, Translator $translator, ActionsService $actionsService)
    {
        $this->conditions = $conditions;
        $this->translator = $translator;
        $this->actionsService = $actionsService;
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
        $typePlaceholder = $this->actionsService->getConditionLocalisationPlaceholder($condition->type);
        $text = Html::el('span', $this->translator->translate($typePlaceholder) . ' ');
        $value = Html::el('span', $condition->params);
        $value->class[] = 'value';

        $el = Html::el('div');
        $el[] = $text;
        $el[] = $value;
        return $el;
    }
}
