<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use App\LeanMapper\TransactionManager;
use CP\TreasureHunt\Model\Entity\Action;
use CP\TreasureHunt\Model\Entity\ActionCondition;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Repository\ActionConditionRepository;
use CP\TreasureHunt\Model\Repository\ActionRepository;
use CP\TreasureHunt\Model\Repository\ChallengeRepository;
use Ublaboo\DataGrid\DataSource\IDataSource;

class ChallengesService
{
    /** @var ChallengeRepository */
    private $challengeRepository;
    /** @var ActionRepository */
    private $actionRepository;
    /** @var ActionConditionRepository */
    private $actionConditionRepository;
    /** @var TransactionManager */
    private $transactionManager;

    public function __construct(
        ChallengeRepository $challengeRepository,
        ActionRepository $actionRepository,
        ActionConditionRepository $actionConditionRepository,
        TransactionManager $transactionManager
    ) {
        $this->challengeRepository = $challengeRepository;
        $this->actionRepository = $actionRepository;
        $this->actionConditionRepository = $actionConditionRepository;
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
        $this->challengeRepository->persist($challenge);
    }

    public function saveAction(Action $action, array $conditions = null)
    {
        $conditionsEntities = $conditions ? array_map(function ($cond) use ($action) {
            if (!$cond instanceof ActionCondition) {
                $cond = new ActionCondition($cond);
            }
            $cond->action = $action;
            return $cond;
        }, $conditions) : [];

        $this->transactionManager->execute(function () use ($action, $conditionsEntities) {
            $this->actionRepository->persist($action);
            $this->actionConditionRepository->deleteByAction($action);
            $this->actionConditionRepository->persistMany($conditionsEntities);
        });
    }

    public function getActionsDataSource(?Challenge $challenge)
    {
        return $this->actionRepository->getEntityDataSource(['challenge' => $challenge]);
    }

    public function getAction(string $id): ?Action
    {
        return $this->actionRepository->findOneBy(['id' => $id]);
    }
}
