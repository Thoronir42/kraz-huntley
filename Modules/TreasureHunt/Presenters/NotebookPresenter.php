<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use App\Security\HasAppUser;
use CP\TreasureHunt\Components\Notebook\NotebookControlFactory;
use CP\TreasureHunt\Executives\Triggers\AnswerSubmitted;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Service\NotebookService;
use CP\TreasureHunt\Model\Service\TreasureHuntService;
use Nette\Application\UI\Presenter;
use Nette\InvalidStateException;

class NotebookPresenter extends Presenter
{
    private const MIN_PAGE_COUNT = 24;

    use HasAppUser;

    /** @var NotebookService @inject */
    public $notebookService;

    /** @var NotebookControlFactory @inject */
    public $notebookControlFactory;

    /** @var TreasureHuntService @inject */
    public $treasureHuntService;

    public function checkRequirements($element): void
    {
        parent::checkRequirements($element);

        if (!$this->user->isLoggedIn()) {
            $this->redirect('TreasureHunt:intro');
        }
    }

    public function actionPage(int $page = 1)
    {
        $notebook = $this->notebookService->getNotebookByUser($this->appUser);
        if (!$notebook) {
            $notebook = $this->notebookService->createNotebook($this->appUser);
        }

        $this['notebook'] = $notebookControl = $this->notebookControlFactory->create($notebook, self::MIN_PAGE_COUNT);
        $this->template->pageNumber = $page;

        if ($page < 1 || ($page > self::MIN_PAGE_COUNT && $page > $notebook->countPages())) {
            $this->redirect('this', ['page' => 1]);
        }

        $notebookControl->onAnswerSubmit[] = function (Challenge $challenge, $answer) use ($notebook) {
            $trigger = new AnswerSubmitted($notebook, $challenge, $answer);
            $result = $this->treasureHuntService->triggerSubmitAnswer($trigger);
            $activePage = $result->getData()['activePage'];
            $this->redirect('this', $activePage);
        };
    }

    protected function beforeRender()
    {
        $this->setView('notebook');
    }
}
