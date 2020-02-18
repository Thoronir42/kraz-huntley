<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use CP\TreasureHunt\Model\Entity\NotebookPage;
use CP\TreasureHunt\Model\Entity\NotebookPageIndex;
use Nette\Application\UI;
use Nette\InvalidArgumentException;

class IndexPage extends UI\Control
{
    /** @var NotebookPageIndex */
    private $page;
    /** @var int */
    private $activePage;

    public function __construct(NotebookPageIndex $page, int $activePage)
    {
        $this->page = $page;
        $this->activePage = $activePage;
    }

    public function render()
    {
        $this->template->indexEntries = $this->filterPages($this->page->getPages());
        $this->template->render(__DIR__ . '/indexPage.latte');
    }

    /**
     * @param NotebookPage[] $pages
     *
     * @return NotebookPage[]
     */
    private function filterPages(array $pages)
    {
        $result = [];
        foreach ($pages as $page) {
            if ($page->type !== NotebookPage::TYPE_INDEX) {
                $result[] = $page;
            }
        }

        return $result;
    }

}
