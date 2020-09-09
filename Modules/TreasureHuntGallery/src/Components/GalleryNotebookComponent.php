<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery\Components;

use CP\TreasureHunt\Model\Entity\Challenge;
use Nette\Application\UI;


class GalleryNotebookComponent extends UI\Control
{
    public function renderChallenge(Challenge $challenge)
    {
        $this['pageComponent'] = $this->template->pageComponent = new GalleryNotebookChallengePage($challenge);

        $this->template->setFile(__DIR__ . '/galleryNotebookComponent.latte');

        $this->template->render();
    }
}
