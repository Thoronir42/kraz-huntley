<?php declare(strict_types=1);

namespace SeStep\Executives\Components;

use SeStep\Executives\Model\Entity\Action;
use Ublaboo\DataGrid\DataGrid;

class ActionGridFactory
{
    public function create(): DataGrid
    {
        $grid = new DataGrid();
        $grid->setPagination(false);

        $grid->addColumnNumber('sequence', '#');

        $grid->addColumnText('type', 'Akce')
            ->setRenderer(function (Action $action) {
                return (new ActionView($action))->getHtml();
            });

        $grid->addColumnText('conditions', 'Podmínky')
            ->setRenderer(function (Action $action) {
                return (new ConditionsList($action->conditions))->getHtml();
            });
        $grid->setDefaultSort('sequence');

        return $grid;
    }
}
