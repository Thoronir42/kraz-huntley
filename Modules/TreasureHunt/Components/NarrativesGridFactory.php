<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components;

use Ublaboo\DataGrid\DataGrid;

class NarrativesGridFactory
{
    public function create()
    {
        $grid = new DataGrid();

        $grid->addColumnText('id', 'ID');
        $grid->addColumnText('title', 'Název');

        $grid->setPagination(false);

        return $grid;
    }
}
