<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery\Components;

use CP\TreasureHunt\Model\Entity\Challenge;
use Nette\Application\UI;


class GalleryNotebookChallengePage extends UI\Control
{
    /** @var Challenge */
    private $challenge;

    public function __construct(Challenge $challenge)
    {
        $this->challenge = $challenge;
    }

    public function render()
    {
        $this->template->challenge = $this->challenge;

        $this->template->render(__DIR__ . '/galleryNotebookChallengePage.latte');
    }
}
