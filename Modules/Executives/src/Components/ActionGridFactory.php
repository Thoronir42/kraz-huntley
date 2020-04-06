<?php declare(strict_types=1);

namespace SeStep\Executives\Components;

use Contributte\Translation\Translator;
use Nette\Utils\Html;
use SeStep\Executives\Model\Entity\Action;
use SeStep\Executives\Model\Service\ActionsService;
use Ublaboo\DataGrid\DataGrid;

class ActionGridFactory
{
    /** @var Translator */
    private $translator;
    /** @var ActionsService */
    private $actionsService;

    public function __construct(Translator $translator, ActionsService $actionsService)
    {
        $this->translator = $translator;
        $this->actionsService = $actionsService;
    }

    public function create(): DataGrid
    {
        $grid = new DataGrid();
        $grid->setTranslator($this->translator);

        $grid->setPagination(false);

        $grid->addColumnNumber('sequence', 'exe.actionSequence');

        $grid->addColumnText('_actionColumn', 'exe.action')
            ->setRenderer(function (Action $action) {
                $element = Html::el('div');
                $element[] = (new ActionView($action, $this->translator, $this->actionsService))->getHtml();
                $element[] = (new ConditionsList($action->conditions, $this->translator, $this->actionsService))->getHtml();
                return $element;
            });
        $grid->setDefaultSort('sequence');

        return $grid;
    }
}
