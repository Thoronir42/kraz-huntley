<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use CP\TreasureHunt\Model\Service\NarrativesService;
use Nette\Application\UI\Presenter;

class CluePresenter extends Presenter
{
    /** @var NarrativesService @inject */
    public $narrativesService;

    public function renderNarrative(string $narrativeId)
    {
        $this->template->narrative = $this->narrativesService->getNarrative($narrativeId);
    }
}
