<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use CP\TreasureHunt\Model\Entity\Action;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Repository\ActionRepository;
use CP\TreasureHunt\Model\Repository\ChallengeRepository;
use Ublaboo\DataGrid\DataSource\IDataSource;

class ChallengesService
{
    /** @var ChallengeRepository */
    private $challengeRepository;
    /** @var ActionRepository */
    private $actionRepository;

    public function __construct(ChallengeRepository $challengeRepository, ActionRepository $actionRepository)
    {
        $this->challengeRepository = $challengeRepository;
        $this->actionRepository = $actionRepository;
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

    public function saveAction(Action $action)
    {
        $this->actionRepository->persist($action);
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
