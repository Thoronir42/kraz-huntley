<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Repository\ChallengeRepository;
use Ublaboo\DataGrid\DataSource\IDataSource;

class ChallengesService
{
    /** @var ChallengeRepository */
    private $challengeRepository;

    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
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
}
