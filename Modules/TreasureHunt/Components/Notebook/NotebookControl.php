<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use CP\TreasureHunt\Model\Entity;
use CP\TreasureHunt\Model\Service\ChallengesService;
use CP\TreasureHunt\Model\Service\NotebookService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Multiplier;
use Nette\ComponentModel\IComponent;
use Nette\InvalidStateException;
use Nette\Localization\ITranslator;
use Nette\NotImplementedException;
use SeStep\NetteTypeful\Forms\PropertyControlFactory;

class NotebookControl extends Control
{
    public $onAnswerSubmit = [];
    public $onFollowRevelation = [];

    /** @var Entity\Notebook */
    private $notebook;
    /** @var int */
    private $minPageCount;

    /** @var ChallengesService */
    private $challengesService;
    /** @var PropertyControlFactory */
    private $propertyControlFactory;
    /** @var ITranslator */
    private $translator;
    /** @var NotebookService */
    private $notebookService;

    // FIXME: Passing dependencies of individual pages is not optimal
    public function __construct(
        Entity\Notebook $notebook,
        int $minPageCount,
        ChallengesService $challengesService,
        PropertyControlFactory $propertyControlFactory,
        ITranslator $translator,
        NotebookService $notebookService
    ) {
        $this->notebook = $notebook;
        $this->minPageCount = $minPageCount;
        $this->challengesService = $challengesService;
        $this->propertyControlFactory = $propertyControlFactory;
        $this->translator = $translator;
        $this->notebookService = $notebookService;
    }

    public function render(int $page)
    {
        $this->template->setFile(__DIR__ . "/notebook.latte");
        $this->template->pageNumber = $page;

        $this->template->render();
    }

    public function createComponentPagination()
    {
        return new NotebookPagination(max($this->notebook->countPages(), $this->minPageCount));
    }

    public function createComponentPage()
    {
        return new Multiplier(function ($pageNumber) {
            $page = $this->notebook->getPage((int)$pageNumber);
            if (!$page) {
                return new EmptyPage();
            }

            switch ($page->type) {
                case Entity\NotebookPage::TYPE_INDEX:
                    /** @var Entity\NotebookPageIndex $page */
                    return new IndexPage($page, $this->notebook->activePage, $this->challengesService);

                case Entity\NotebookPage::TYPE_CHALLENGE:
                    /** @var Entity\NotebookPageChallenge $page */
                    $challengePage = new ChallengePage($page,
                        $this->challengesService->getChallenge($page->getChallengeId()),
                        $this->propertyControlFactory,
                        $this->translator,
                        $this->notebookService,
                    );
                    $challengePage->onAnswerSubmit = &$this->onAnswerSubmit;
                    $challengePage->onFollowRevelation = &$this->onFollowRevelation;

                    return $challengePage;

                default:
                    throw new NotImplementedException("Page type '{$page->type}' not recognized");
            }
        });
    }
}
