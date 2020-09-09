<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;


use App\Grid\MappingDataSource;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\InputBan;
use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Service\NotebookService;
use Nette\Application\UI\Presenter;
use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;

class PlayersPresenter extends Presenter
{

    /** @var NotebookService @inject */
    public $notebookService;

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

        $notebooksGrid = $this['notebooksGrid'];

        $notebooksGrid->setDataSource($dataSource);

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
            $content->addHtml(Html::el('small', $challenge->code));
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
}
