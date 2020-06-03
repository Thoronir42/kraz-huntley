<?php

namespace CP\TreasureHunt\Components\Notebook;

use Nette\Application\UI;

class NotebookPagination extends UI\Control
{

    /** @var int */
    private $maxPage;

    public function __construct(int $maxPage)
    {
        $this->maxPage = $maxPage;
    }

    public function render(int $pageNumber)
    {
        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/notebookPagination.latte');
        $template->pageNumber = $pageNumber;
        $template->minPage = 1;
        $template->maxPage = $this->maxPage;

        $template->render();
    }
}
