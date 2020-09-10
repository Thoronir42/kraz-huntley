<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;


use App\Grid\MappingDataSource;
use App\Security\UserManager;
use CP\TreasureHunt\Controls\EmojiMatrixControl;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\InputBan;
use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Entity\NotebookPage;
use CP\TreasureHunt\Model\Entity\NotebookPageChallenge;
use CP\TreasureHunt\Model\Repository\ChallengeRepository;
use CP\TreasureHunt\Model\Service\NotebookService;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;

class PlayersPresenter extends Presenter
{

    /** @var NotebookService @inject */
    public $notebookService;

    /** @var ChallengeRepository @inject */
    public $challengeRepository;

    /** @var UserManager @inject */
    public $userManager;

    protected function beforeRender()
    {
        parent::beforeRender();
        $this->setLayout('meta');
    }

    public function renderIndex()
    {
        $dataSource = new MappingDataSource($this->notebookService->getDataSource(),
            \Closure::fromCallable(function (Notebook $notebook, $id, $relatedData) {
                return [
                    'id' => $id,
                    'notebook' => $notebook,
                    'activePage' => $relatedData['activePages'][$notebook->id] ?? null,
                    'challenge' => $relatedData['challenges'][$notebook->id] ?? null,
                    'inputBan' => $relatedData['inputBans'][$notebook->id] ?? null,
                ];
            }));

        $dataSource->setLoadRelatedData(\Closure::fromCallable(function ($notebooks) {
            $activePages = $this->notebookService->getActivePages($notebooks);

            return [
                'activePages' => $activePages,
                'challenges' => $this->notebookService->getPageChallenges($activePages),
                'inputBans' => $this->notebookService->getPageInputBans($activePages, false),
            ];
        }));

        /** @var DataGrid $notebooksGrid */
        $notebooksGrid = $this['notebooksGrid'];

        $notebooksGrid->setDataSource($dataSource);
        $notebooksGrid->addAction('detail', 'Detail', 'detail');
    }

    public function actionDetail(string $id)
    {
        $notebook = $this->notebookService->findNotebook($id);
        $hunter = $notebook->user;

        $this->template->hunter = $hunter;

        /** @var DataGrid $playerChallengesGrid */
        $playerChallengesGrid = $this['playerChallengesGrid'];
        $pagesDataSource = $this->notebookService->getPagesDataSource($notebook);
        $mappedDataSource = new MappingDataSource($pagesDataSource,
            \Closure::fromCallable(function (NotebookPage $page, $id, $related) use ($notebook) {
                return [
                    'id' => $id,
                    'pageNumber' => $page->pageNumber,
                    'active' => $page->pageNumber === $notebook->activePage,
                    'page' => $page,
                    'challenge' => $related['challenges'][$id] ?? null,
                    'discoverdOn' => $page->discoveredOn,
                ];
            }));
        $mappedDataSource->setLoadRelatedData(function ($pages) {
            $challengeIds = [];
            foreach ($pages as $page) {
                if ($page instanceof NotebookPageChallenge) {
                    $challengeIds[$page->id] = $page->getChallengeId();
                }
            }

            return [
                'challenges' => $this->challengeRepository->browse($challengeIds),
            ];
        });

        $playerChallengesGrid->setDataSource($mappedDataSource);

        $playerChallengesGrid->setPagination(false);

        /** @var Form $setPasswordForm */
        $setPasswordForm = $this['setPasswordForm'];
        $setPasswordForm->onSuccess[] = function ($form, $values) use ($hunter) {
            $pass = implode('-', $values['password']);
            $this->userManager->updatePassword($hunter, $pass);
            $this->flashMessage('Heslo nastaveno');
            $this->redirect('this');
        };
    }

    public function createComponentNotebooksGrid()
    {
        $grid = new DataGrid();
        $grid->addColumnText('hunter', 'Lovec')->setRenderer(function ($row) {
            $content = Html::el('div');
            $content->addHtml(Html::el('span', $row['notebook']->user->nick));
            $content->addHtml(Html::el('small', " ({$row['notebook']->id})"));

            return $content;
        });
        $grid->addColumnText('activePage', 'Aktivní stránka')->setRenderer(function ($row) {
            return $row['activePage'] ? $row['activePage']->pageNumber : 'N/A';
        });
        $grid->addColumnText('currentChallenge', 'Aktuální výzva')->setRenderer(function ($row) {
            if (!$row['challenge']) {
                return 'N/A';
            }
            /** @var Challenge $challenge */
            $challenge = $row['challenge'];
            /** @var InputBan $inputBan */
            $inputBan = $row['inputBan'];
            $content = Html::el('div');
            $challengeLink = Html::el('a', "({$challenge->code})");
            $challengeLink->addAttributes([
                'class' => 'pr-2',
                'href' => $this->link('Challenges:detail', $challenge->id),
            ]);
            $content->addHtml($challengeLink);
            $content->addHtml(Html::el('span', $challenge->title));
            if ($inputBan) {
                $content->addHtml(Html::el('br'));
                $content->addHtml(Html::el('small',
                    "Odpovídání blokováno do: " . $inputBan->activeUntil->format('H:i:s')));
            }

            return $content;
        });

        return $grid;
    }

    public function createComponentPlayerChallengesGrid()
    {
        $grid = new DataGrid();
        $grid->addColumnNumber('number', 'Č. stránky', 'pageNumber')
            ->setRenderer(function ($row) {
                $content = Html::el('div', $row['pageNumber']);
                if ($row['active']) {
                    $content->addHtml(Html::el('small', " (aktivní)"));
                }
                return $content;
            });
        $grid->addColumnText('type', 'Typ')->setRenderer(function ($row) {
            return $row['page']->type;
        });
        $grid->addColumnText('details', 'Podrobnosti')->setRenderer(function ($row) {
            $page = $row['page'];
            $content = Html::el('div');
            if ($page instanceof NotebookPageChallenge) {
                /** @var Challenge $challenge */
                $challenge = $row['challenge'];
                $challengeLink = Html::el('a', "({$challenge->code})");
                $challengeLink->addAttributes([
                    'class' => 'pr-2',
                    'href' => $this->link('Challenges:detail', $challenge->id),
                ]);
                $content->addHtml($challengeLink);
                $content->addHtml(Html::el('span', $challenge->title));
            }

            return $content;
        });
        $grid->addColumnDateTime('discoverdOn', 'Odhalena')
            ->setFormat('d.m. H:i:s');

        return $grid;
    }

    public function createComponentSetPasswordForm()
    {
        $form = new Form();
        $form['password'] = new EmojiMatrixControl('Nové heslo');
        $form->addSubmit('save');

        return $form;
    }
}
