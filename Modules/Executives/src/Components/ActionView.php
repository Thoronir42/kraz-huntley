<?php declare(strict_types=1);

namespace SeStep\Executives\Components;

use Contributte\Translation\Translator;
use Nette\Application\UI;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;
use SeStep\Executives\Model\Entity\Action;
use SeStep\Executives\Model\Service\ActionsService;

class ActionView extends UI\Component
{
    /** @var Action */
    private $action;

    /** @var Translator */
    private $translator;
    /** @var ActionsService */
    private $actionsService;

    public function __construct(Action $action, Translator $translator, ActionsService $actionsService)
    {
        $this->action = $action;
        $this->translator = $translator;
        $this->actionsService = $actionsService;
    }

    public function getHtml()
    {
        $placeholder = $this->actionsService->getActionLocalisationPlaceholder($this->action->type);
        $text = Html::el('span', $this->translator->translate($placeholder));
        $value = Html::el('span', $this->action->params);
        $value->class[] = 'value';
        $el = Html::el('div');
        $el[] = $text;
        $el[] = $value;

        return $el;
    }

    public function createComponentConditions()
    {
        return new ConditionsList($this->action->conditions);
    }
}
