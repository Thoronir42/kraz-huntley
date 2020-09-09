<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery\Presenters;


use App\Security\HasAppUser;
use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Service\ChallengesService;
use CP\TreasureHunt\Model\Service\NotebookService;
use CP\TreasureHuntGallery\Components\GalleryNotebookComponent;
use CP\TreasureHuntGallery\Model\Services\GalleryService;
use Nette\Application\UI\Presenter;

class GalleryPresenter extends Presenter
{
    use HasAppUser;

    public $layout = 'meta';

    /** @var GalleryService @inject */
    public $galleryService;
    /** @var ChallengesService @inject */
    public $challengeService;
    /** @var NotebookService @inject */
    public $notebookService;

    public function checkRequirements($element): void
    {
        if (!$this->appUser || !$this->galleryService->hasAccess($this->appUser)) {
            $this->flashMessage('messages.forbidden');
            $this->redirect(':TreasureHunt:TreasureHunt:sign');
        }
    }

    public function renderIndex()
    {
        $notebook = $this->notebookService->getNotebookByUser($this->appUser);

        $visitedChallenges = [];
        foreach ($this->galleryService->getUnlockedChallengeIds($notebook) as $challengeId) {
            $visitedChallenges[$challengeId] = true;
        }

        $this->template->challenges = $this->challengeService->getChallengesDataSource()->getData();
        $this->template->visitedChallenges = $visitedChallenges;
    }

    public function renderChallenge(string $id)
    {
        $challenge = $this->challengeService->getChallenge($id);
        if (!$challenge) {
            $this->redirect('index');
            return;
        }

        $this->template->challenge = $challenge;
    }

    public function formatLayoutTemplateFiles(): array
    {
        $dir = dirname(__DIR__, 3) . '/TreasureHunt/templates';

        return [
            "$dir/@{$this->layout}.latte",
        ];
    }

    public function createComponentGalleryNotebook()
    {
        return new GalleryNotebookComponent();
    }
}
