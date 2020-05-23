<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use App\LeanMapper\TransactionManager;
use App\Model\Entity\User;
use CP\TreasureHunt\Components\Notebook\IndexPage;
use CP\TreasureHunt\Executives\Actions\InitializeNotebookAction;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Entity\NotebookPage;
use CP\TreasureHunt\Model\Repository\NotebookPageRepository;
use CP\TreasureHunt\Model\Repository\NotebookRepository;
use SeStep\Executives\Execution\ActionExecutor;
use SeStep\Executives\Execution\ClassnameActionExecutor;
use SeStep\Executives\ModuleAggregator;

class NotebookService
{
    private const PAGE_TYPES = [
        NotebookPage::TYPE_INDEX,
        NotebookPage::TYPE_CHALLENGE,
    ];

    /** @var NotebookRepository */
    private $notebookRepository;
    /** @var NotebookPageRepository */
    private $notebookPageRepository;
    /** @var TransactionManager */
    private $transactionManager;
    /** @var ClassnameActionExecutor */
    private $classnameActionExecutor;

    public function __construct(
        NotebookRepository $notebookRepository,
        NotebookPageRepository $notebookPageRepository,
        TransactionManager $transactionManager,
        ClassnameActionExecutor $classnameActionExecutor
    ) {
        $this->notebookRepository = $notebookRepository;
        $this->notebookPageRepository = $notebookPageRepository;
        $this->transactionManager = $transactionManager;
        $this->classnameActionExecutor = $classnameActionExecutor;
    }

    public function getPageTypes(): array
    {
        return self::PAGE_TYPES;
    }

    public function getNotebook(string $id): ?Notebook
    {
        return $this->notebookRepository->find($id);
    }

    public function getNotebookByUser(User $user): ?Notebook
    {
        return $this->notebookRepository->findOneBy([
            'user' => $user,
        ]);
    }

    public function createNotebook(User $user): Notebook
    {
        return $this->transactionManager->execute(function () use ($user) {
            $notebook = $this->notebookRepository->create($user, 1);

            $context = new \stdClass();
            $context->notebook = $notebook;

            $this->classnameActionExecutor->execute(InitializeNotebookAction::class, [
                'challengeId' => 'CZv9',
            ], $context);

            return $notebook;
        });
    }

    public function activateChallengePage(Notebook $notebook, Challenge $challenge): bool
    {
        return $this->transactionManager->execute(function () use ($notebook, $challenge) {
            $challengePage = $this->notebookPageRepository->findOneBy([
                'notebook' => $notebook,
                'params' => '"challengeId": "' . $challenge->id . '"',
            ]);

            if (!$challengePage) {
                $challengePage = $this->notebookPageRepository->createChallenge($notebook, $challenge);
            }

            $notebook->activePage = $challengePage->pageNumber;
            $this->notebookRepository->persist($notebook);

            return true;
        });
    }
}
