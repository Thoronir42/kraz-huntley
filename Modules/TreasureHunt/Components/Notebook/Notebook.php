<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use CP\TreasureHunt\Model\Entity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Multiplier;
use Nette\ComponentModel\IComponent;
use Nette\InvalidStateException;

class Notebook extends Control
{
    /** @var Entity\Notebook */
    private $notebook;

    public function __construct(Entity\Notebook $notebook)
    {
        $this->notebook = $notebook;
    }

    public function render(int $page)
    {
        $notebookPage = $this->notebook->getPage($page, true);

        $this->template->setFile(__DIR__ . "/notebook.latte");
        $this->template->page = $notebookPage;

        $this->template->render();
    }

    public function createComponentPage()
    {
        return new Multiplier(function ($pageNumber) {
            $page = $this->notebook->getPage((int)$pageNumber);

            switch ($page->type) {
                case Entity\NotebookPage::TYPE_INDEX:
                    return new IndexPage($page, $this->notebook->activePage);
                case Entity\NotebookPage::TYPE_CHALLENGE:
                    return new ChallengePage($page);
            }

            return null;
        });

    }
}
