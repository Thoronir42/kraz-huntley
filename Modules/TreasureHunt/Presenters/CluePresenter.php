<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use App\Security\HasAppUser;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Service\NarrativesService;
use CP\TreasureHunt\Model\Service\NotebookService;
use CP\TreasureHunt\Model\Service\TreasureMapsService;
use Nette\Application\BadRequestException;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;

class CluePresenter extends Presenter
{
    use HasAppUser;

    /** @var NarrativesService @inject */
    public $narrativesService;

    /** @var TreasureMapsService @inject */
    public $treasureMapsService;

    /** @var NotebookService @inject */
    public $notebookService;

    /** @var Challenge */
    private $followingChallenge;

    public function checkRequirements($element): void
    {

        if ($this->request->method !== Request::FORWARD && !isset($this->params['do'])) {
            die('Nelegální požadavek. Huuiiii-iiuuuu, huuiiii-iiuuuu');
        }
    }

    public function actionNarrative(string $narrativeId)
    {
        $narrative = $this->narrativesService->getNarrative($narrativeId);
        if (!$narrative) {
            throw new BadRequestException("Narrative not found");
        }
        $this->followingChallenge = $narrative->followingChallenge;

        $this->template->narrative = $narrative;
    }

    public function renderMap(string $mapId)
    {
        $map = $this->treasureMapsService->getMap($mapId, true);
        if (!$map) {
            throw new BadRequestException("Treasure map not found");
        }

        $this->template->map = $map;
    }

    public function handleContinueToNextChallenge()
    {
        $notebook = $this->notebookService->getNotebookByUser($this->appUser);
        $activePage = $this->notebookService->activateChallengePage($notebook, $this->followingChallenge);

        $this->redirect('Notebook:page', $activePage);
    }
}
