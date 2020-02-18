<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use App\Security\HasAppUser;
use CP\TreasureHunt\Components\Notebook\Notebook;
use CP\TreasureHunt\Model\Service\NotebookService;
use Nette\Application\UI\Presenter;

class NotebookPresenter extends Presenter
{
    uSe HasAppUser;

    /** @var NotebookService @inject */
    public $notebookService;

    public function actionIndex()
    {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('TreasureHunt:intro');
        }

        $notebook = $this->notebookService->getNotebookByUser($this->appUser, true);

        $this['notebook'] = new Notebook($notebook);
        $this->template->pageNumber = 1;
    }

    public function actionPage(int $page)
    {
        if ($page <= 1) {
            $this->redirect('index');
        }
    }

    protected function beforeRender()
    {
        $this->setView('notebook');
    }
}
