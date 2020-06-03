<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use CP\TreasureHunt\Model\Entity\Notebook;

interface NotebookControlFactory
{
    public function create(Notebook $notebook, int $minPageCount): NotebookControl;
}
