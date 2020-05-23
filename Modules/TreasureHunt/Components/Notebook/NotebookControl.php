<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use CP\TreasureHunt\Model\Entity;
use CP\TreasureHunt\Model\Service\ChallengesService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Multiplier;
use Nette\ComponentModel\IComponent;
use Nette\InvalidStateException;
use Nette\NotImplementedException;
use SeStep\Typeful\Forms\PropertyControlFactory;

class NotebookControl extends Control
{
    /** @var Entity\Notebook */
    private $notebook;
    /** @var ChallengesService */
    private $challengesService;
    /** @var PropertyControlFactory */
    private $propertyControlFactory;

    public function __construct(
        Entity\Notebook $notebook,
        ChallengesService $challengesService,
        PropertyControlFactory $propertyControlFactory
    ) {
        $this->notebook = $notebook;
        $this->challengesService = $challengesService;
        $this->propertyControlFactory = $propertyControlFactory;
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
                    /** @var Entity\NotebookPageIndex $page */
                    return new IndexPage($page, $this->notebook->activePage);

                case Entity\NotebookPage::TYPE_CHALLENGE:
                    /** @var Entity\NotebookPageChallenge $page */
                    return new ChallengePage($page,
                        $this->challengesService->getChallenge($page->getChallengeId()),
                        $this->propertyControlFactory);

                default:
                    throw new NotImplementedException("Page type '{$page->type}' not recognized");
            }
        });
    }
}
