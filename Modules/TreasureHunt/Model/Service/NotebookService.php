<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use App\LeanMapper\TransactionManager;
use App\Model\Entity\User;
use CP\TreasureHunt\Executives\Actions\InitializeNotebookAction;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\InputBan;
use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Entity\NotebookPage;
use CP\TreasureHunt\Model\Repository\InputBanRepository;
use CP\TreasureHunt\Model\Repository\NotebookPageRepository;
use CP\TreasureHunt\Model\Repository\NotebookRepository;
use DateTime;
use Nette\Neon\Neon;
use SeStep\Executives\Execution\ClassnameActionExecutor;

class NotebookService
{
    /** @var NotebookRepository */
    private $notebookRepository;
    /** @var NotebookPageRepository */
    private $notebookPageRepository;
    /** @var InputBanRepository */
    private $inputBanRepository;
    /** @var TransactionManager */
    private $transactionManager;
    /** @var ClassnameActionExecutor */
    private $classnameActionExecutor;
    /** @var string */
    private $firstChallengeId;

    public function __construct(
        NotebookRepository $notebookRepository,
        NotebookPageRepository $notebookPageRepository,
        InputBanRepository $inputBanRepository,
        TransactionManager $transactionManager,
        ClassnameActionExecutor $classnameActionExecutor,
        string $firstChallengeId
    ) {
        $this->notebookRepository = $notebookRepository;
        $this->notebookPageRepository = $notebookPageRepository;
        $this->inputBanRepository = $inputBanRepository;
        $this->transactionManager = $transactionManager;
        $this->classnameActionExecutor = $classnameActionExecutor;
        $this->firstChallengeId = $firstChallengeId;
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
                'challengeId' => $this->firstChallengeId,
            ], $context);

            return $notebook;
        });
    }

    public function activateChallengePage(Notebook $notebook, Challenge $challenge): ?int
    {
        return $this->transactionManager->execute(function () use ($notebook, $challenge) {
            $challengePage = $this->notebookPageRepository->findOneBy([
                'notebook' => $notebook,
                'params' => '%"challengeId":"' . $challenge->id . '"%',
            ]);

            if (!$challengePage) {
                $challengePage = $this->notebookPageRepository->createChallenge($notebook, $challenge);
            }

            $notebook->activePage = $challengePage->pageNumber;
            $this->notebookRepository->persist($notebook);

            return $notebook->activePage;
        });
    }

    /**
     * @return string
     */
    public function getFirstChallengeId(): string
    {
        return $this->firstChallengeId;
    }

    public function setFirstChallengeId(string $id)
    {
        // FIXME: WOW, Ugly!
        $file = __DIR__ . '/../../../../config/config.local.neon';
        $neon = Neon::decode(file_get_contents($file));

        $neon['parameters']['firstChallengeId'] = $id;

        file_put_contents($file, Neon::encode($neon, Neon::BLOCK));
    }

    public function addInputBan(NotebookPage $page, DateTime $activeUntil): InputBan
    {
        $inputBan = new InputBan();
        $inputBan->notebookPage = $page;
        $inputBan->activeUntil = $activeUntil;

        $this->inputBanRepository->persist($inputBan);

        return $inputBan;
    }

}
