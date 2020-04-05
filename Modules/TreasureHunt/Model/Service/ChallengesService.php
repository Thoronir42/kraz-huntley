<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use App\LeanMapper\TransactionManager;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Repository\ChallengeRepository;
use SeStep\Executives\Model\Entity\Script;
use SeStep\Executives\Model\Service\ActionsService;
use Ublaboo\DataGrid\DataSource\IDataSource;

class ChallengesService
{
    /** @var ActionsService */
    private $actionsService;
    /** @var ChallengeRepository */
    private $challengeRepository;
    /** @var TransactionManager */
    private $transactionManager;

    public function __construct(
        ActionsService $actionsService,
        ChallengeRepository $challengeRepository,
        TransactionManager $transactionManager
    ) {
        $this->actionsService = $actionsService;
        $this->challengeRepository = $challengeRepository;
        $this->transactionManager = $transactionManager;
    }

    public function getChallenge(string $id): ?Challenge
    {
        return $this->challengeRepository->findOneBy(['id' => $id]);
    }

    public function getChallengesDataSource(): IDataSource
    {
        return $this->challengeRepository->getEntityDataSource();
    }

    public function save(Challenge $challenge)
    {
        $this->transactionManager->execute(function () use ($challenge) {
            if ($challenge->isDetached()) {
                $script = $this->actionsService->createScript(Script::METHOD_STOP_ON_FIRST_PASS);
                $challenge->submitScript = $script;
            }

            $this->challengeRepository->persist($challenge);
        });

    }
}
