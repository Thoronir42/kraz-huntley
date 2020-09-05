<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use App\Security\HasAppUser;
use CP\TreasureHunt\Components\Notebook\NotebookControlFactory;
use CP\TreasureHunt\Executives\NavigationResultBuilder;
use CP\TreasureHunt\Executives\Triggers\AnswerSubmitted;
use CP\TreasureHunt\HandleNavigationAdvance;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\ClueRevelation;
use CP\TreasureHunt\Model\Service\NotebookService;
use CP\TreasureHunt\Model\Service\TreasureHuntService;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;

class NotebookPresenter extends Presenter
{
    private const MIN_PAGE_COUNT = 24;

    use HasAppUser;
    use HandleNavigationAdvance;

    /** @var NotebookService @inject */
    public $notebookService;

    /** @var NotebookControlFactory @inject */
    public $notebookControlFactory;

    /** @var TreasureHuntService @inject */
    public $treasureHuntService;

    /** @var ITranslator @inject */
    public $translator;

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

        $notebookControl->onAnswerSubmit[] = function (Challenge $challenge, $answer) use ($notebook, $page) {
            $currentPage = $notebook->getPage($page, true);

            $trigger = new AnswerSubmitted($notebook, $challenge, $currentPage, $answer);
            $result = $this->treasureHuntService->triggerSubmitAnswer($trigger);

            $this->checkAdvance($result);
        };
        
        $notebookControl->onFollowRevelation[] = function (ClueRevelation $revelation) {
            $navigation = NavigationResultBuilder::forward($revelation->clueType)
                ->withArg('clueArgs', $revelation->clueArgs)
                ->build();

            $this->checkAdvance($navigation);
        };
    }

    protected function beforeRender()
    {
        $this->setView('notebook');
    }
}
