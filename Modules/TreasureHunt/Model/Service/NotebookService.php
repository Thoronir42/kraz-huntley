<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use App\LeanMapper\TransactionManager;
use App\Model\Entity\User;
use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Entity\NotebookPage;
use CP\TreasureHunt\Model\Repository\NotebookPageRepository;
use CP\TreasureHunt\Model\Repository\NotebookRepository;

class NotebookService
{
    /** @var NotebookRepository */
    private $notebookRepository;
    /** @var NotebookPageRepository */
    private $notebookPageRepository;
    /** @var TransactionManager */
    private $transactionManager;

    public function __construct(
        NotebookRepository $notebookRepository,
        NotebookPageRepository $notebookPageRepository,
        TransactionManager $transactionManager
    ) {
        $this->notebookRepository = $notebookRepository;
        $this->notebookPageRepository = $notebookPageRepository;
        $this->transactionManager = $transactionManager;
    }

    public function getNotebookByUser(User $user, bool $createIfMissing = false): ?Notebook
    {
        $notebook = $this->notebookRepository->findOneBy([
            'user' => $user,
        ]);
        if (!$notebook && $createIfMissing) {
            $notebook = $this->createNotebook($user);
        }

        return $notebook;
    }

    public function createNotebook(User $user): Notebook
    {
        return $this->transactionManager->execute(function() use ($user) {
            $notebook = $this->notebookRepository->create($user, 1);
            $this->notebookPageRepository->createIndex($notebook);

            return $notebook;
        });
    }

    public function getPageByUser(string $userId, int $page): ?NotebookPage
    {
        $page = $this->notebookPageRepository->findOneBy([
            'notebook.user' => $userId,
            'page' => $page,
        ]);

        dump($page);
        exit;
    }
}
