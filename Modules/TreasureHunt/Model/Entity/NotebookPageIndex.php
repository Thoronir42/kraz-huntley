<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

class NotebookPageIndex extends NotebookPage
{
    /** @var NotebookPage[] */
    private $pages = [];

    /** @return NotebookPage[] */
    public function getPages(): array
    {
        return $this->pages;
    }

    /** @param NotebookPage[] $pages */
    public function setPages(array $pages): void
    {
        $this->pages = $pages;
    }
}
