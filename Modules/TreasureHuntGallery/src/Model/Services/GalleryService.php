<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery\Model\Services;


use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Service\NotebookService;
use CP\TreasureHuntGallery\Model\Repository\ChallengeViewRepository;

class GalleryService
{
    /** @var ChallengeViewRepository */
    private $challengeViewRepository;
    /** @var NotebookService */
    private $notebookService;

    public function __construct(ChallengeViewRepository $challengeViewRepository, NotebookService $notebookService)
    {
        $this->challengeViewRepository = $challengeViewRepository;
        $this->notebookService = $notebookService;
    }

    public function hasAccess(\App\Model\Entity\User $appUser): bool
    {
        $notebook = $this->notebookService->getNotebookByUser($appUser);
        if (!$notebook) {
            return false;
        }

        $count = $this->challengeViewRepository->count([
            'notebook' => $notebook,
        ]);

        return $count > 0;
    }

    public function unlockChallenges(Notebook $notebook, array $challengeIds)
    {
        $this->challengeViewRepository->unlockChallenges($notebook, $challengeIds);
    }

    /**
     * @param Notebook $notebook
     * @return string[]
     */
    public function getUnlockedChallengeIds(Notebook $notebook): array
    {
        return $this->challengeViewRepository->getUnlockedChallenges($notebook);
    }
}
