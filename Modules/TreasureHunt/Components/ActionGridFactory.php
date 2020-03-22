<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components;

use CP\TreasureHunt\Model\Entity\Action;
use Ublaboo\DataGrid\DataGrid;

class ActionGridFactory
{
    public function create(): DataGrid
    {
        $grid = new DataGrid();
        $grid->setPagination(false);

        $grid->addColumnNumber('sequence', '#');

        $grid->addColumnText('type', 'Typ')
            ->setEditableInputTypeSelect([
                Action::TYPE_ACTIVATE_CHALLENGE => 'Aktivovat výzvu',
                Action::TYPE_REVEAL_NARRATIVE => 'Odhalit doprovodný text',
            ]);
        $grid->setDefaultSort('sequence');

        return $grid;
    }
}
