<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use CP\TreasureHunt\Model\Service\NarrativesService;
use CP\TreasureHunt\Model\Service\TreasureMapsService;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;

class CluePresenter extends Presenter
{
    /** @var NarrativesService @inject */
    public $narrativesService;

    /** @var TreasureMapsService @inject */
    public $treasureMapsService;

    public function renderNarrative(string $narrativeId)
    {
        $this->template->narrative = $this->narrativesService->getNarrative($narrativeId);
    }

    public function renderMap(string $mapId)
    {
        $map = $this->treasureMapsService->getMap($mapId);
        if (!$map) {
            throw new BadRequestException("Treasure map not found");
        }

        $this->template->map = $map;
    }
}
