<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components;

use Ublaboo\DataGrid\DataGrid;

class ChallengesGridFactory
{
    public function create(): DataGrid
    {
        $grid = new DataGrid();
        $grid->addColumnText('id', 'ID');
        $grid->addColumnText('code', 'Kód')->setSortable();
        $grid->addColumnText('title', 'Název');

        $grid->setPagination(false);

        return $grid;
    }
}
