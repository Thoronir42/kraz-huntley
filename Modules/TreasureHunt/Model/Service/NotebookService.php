<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use App\LeanMapper\TransactionManager;
use App\Model\Entity\User;
use CP\TreasureHunt\Executives\Actions\InitializeNotebookAction;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\ClueRevelation;
use CP\TreasureHunt\Model\Entity\InputBan;
use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Entity\NotebookPage;
use CP\TreasureHunt\Model\Entity\NotebookPageChallenge;
use CP\TreasureHunt\Model\Repository\ChallengeRepository;
use CP\TreasureHunt\Model\Repository\ClueRevelationRepository;
use CP\TreasureHunt\Model\Repository\InputBanRepository;
use CP\TreasureHunt\Model\Repository\NotebookPageRepository;
use CP\TreasureHunt\Model\Repository\NotebookRepository;
use DateTime;
use Dibi\Expression;
use Nette\Neon\Neon;
use SeStep\Executives\Execution\ClassnameActionExecutor;

class NotebookService
{
    /** @var NotebookRepository */
    private $notebookRepository;
    /** @var NotebookPageRepository */
    private $notebookPageRepository;
    /** @var ClueRevelationRepository */
    private $clueRevelationRepository;
    /** @var InputBanRepository */
    private $inputBanRepository;
    /** @var ChallengeRepository */
    private $challengeRepository;
    /** @var TransactionManager */
    private $transactionManager;
    /** @var ClassnameActionExecutor */
    private $classnameActionExecutor;
    /** @var string */
    private $firstChallengeId;

    public function __construct(
        NotebookRepository $notebookRepository,
        NotebookPageRepository $notebookPageRepository,
        ClueRevelationRepository $clueRevelationRepository,
        InputBanRepository $inputBanRepository,
        ChallengeRepository $challengeRepository,
        TransactionManager $transactionManager,
        ClassnameActionExecutor $classnameActionExecutor,
        string $firstChallengeId
    ) {
        $this->notebookRepository = $notebookRepository;
        $this->notebookPageRepository = $notebookPageRepository;
        $this->clueRevelationRepository = $clueRevelationRepository;
        $this->inputBanRepository = $inputBanRepository;
        $this->challengeRepository = $challengeRepository;
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

    public function findNotebook(string $id): ?Notebook
    {
        return $this->notebookRepository->find($id);
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

    public function addClueRevelation(NotebookPage $page, string $clueType, array $clueArgs, DateTime $expiresOn = null)
    {
        $revelation = new ClueRevelation();
        $revelation->notebookPage = $page;
        $revelation->clueType = $clueType;
        $revelation->clueArgs = $clueArgs;
        $revelation->expiresOn = $expiresOn;

        $revelation->dateCreated = new DateTime();

        $this->clueRevelationRepository->persist($revelation);

        return $revelation;
    }

    public function getCLueRevelations(NotebookPage $page)
    {
        return $this->clueRevelationRepository->findBy([
            'notebookPage' => $page,
        ], ['dateCreated']);
    }

    public function addInputBan(NotebookPage $page, DateTime $activeUntil): InputBan
    {
        $inputBan = new InputBan();
        $inputBan->notebookPage = $page;
        $inputBan->activeUntil = $activeUntil;

        $this->inputBanRepository->persist($inputBan);

        return $inputBan;
    }

    public function findActiveInputBan(NotebookPage $page): ?InputBan
    {
        return $this->inputBanRepository->findOneBy([
            'notebookPage' => $page,
            'activeUntil' => new Expression('> ?', new DateTime()),
        ]);
    }

    public function getDataSource()
    {
        return $this->notebookRepository->getEntityDataSource();
    }

    public function getActivePages(array $notebooks): array
    {
        return $this->notebookPageRepository->getActivePages($notebooks);
    }

    /**
     * @param NotebookPage[] $pages
     * @return Challenge[]
     */
    public function getPageChallenges(array $pages)
    {
        $challengeIds = [];
        foreach ($pages as $key => $page) {
            if ($page instanceof NotebookPageChallenge) {
                $challengeIds[$key] = $page->getChallengeId();
            }
        }

        return $this->challengeRepository->browse($challengeIds);
    }

    /**
     * @param NotebookPage[] $pages
     * @param bool|null $active
     *
     * @return InputBan[]
     */
    public function getPageInputBans(array $pages, ?bool $active = null): array
    {
        $conditions = [
            'notebookPage' => array_values($pages),
        ];

        if (is_bool($active)) {
            $now = new DateTime();
            $expression = $active ? new Expression('> ?', $now) : new Expression('<= ?', $now);
            $conditions['activeUntil'] = $expression;
        }

        $bans = [];
        /** @var InputBan $ban */
        foreach ($this->inputBanRepository->findBy($conditions) as $ban) {
            $bans[$ban->getRowData()['notebook_page_id']] = $ban;
        }

        $result = [];
        foreach ($pages as $key => $page) {
            if (isset($bans[$page->id])) {
                $result[$key] = $bans[$page->id];
            }
        }

        return $result;
    }

    public function getPagesDataSource(Notebook $notebook)
    {
        return $this->notebookPageRepository->getEntityDataSource([
            'notebook' => $notebook,
        ]);
    }

}
