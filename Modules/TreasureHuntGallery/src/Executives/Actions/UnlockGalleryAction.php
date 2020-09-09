<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery\Executives\Actions;


use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Entity\NotebookPage;
use CP\TreasureHunt\Model\Entity\NotebookPageChallenge;
use CP\TreasureHuntGallery\Model\Services\GalleryService;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;

class UnlockGalleryAction implements Action
{
    /** @var GalleryService */
    private $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    public function execute($context, $params): ExecutionResult
    {
        /** @var Notebook $notebook */
        $notebook = $context->notebook;

        $visitedChallengeIds = $this->getVisitedChallengeIds($notebook->pages);
        $this->galleryService->unlockChallenges($notebook, $visitedChallengeIds);

        return ExecutionResult::ok([]);
    }

    /**
     * @param NotebookPage[] $pages
     *
     * @return string[]
     */
    private function getVisitedChallengeIds(array $pages): array
    {
        $challengeIds = [];

        foreach ($pages as $page) {
            if ($page instanceof NotebookPageChallenge) {
                $challengeIds[] = $page->getChallengeId();
            }
        }

        return $challengeIds;
    }
}
