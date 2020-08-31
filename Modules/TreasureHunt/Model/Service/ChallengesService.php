<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use App\LeanMapper\TransactionManager;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Repository\ChallengeRepository;
use SeStep\Executives\Model\ActionData;
use SeStep\Executives\Module\Actions\MultiAction;
use SeStep\Executives\Module\Actions\Strategy\MultiActionStrategy;
use SeStep\LeanExecutives\ActionsService;
use SeStep\LeanExecutives\Entity\Action;
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
            if (!$challenge->onSubmit) {
                $challenge->onSubmit = $this->actionsService->createActionByClass(MultiAction::class, [
                    'strategy' => 'returnOnFirstPass',
                    'actions' => [],
                ]);
            }

            $this->challengeRepository->persist($challenge);
        });

    }

    public function getNames()
    {
        return $this->challengeRepository->listColumn('title');
    }

    public function setOnSubmitAction(Challenge $challenge, ActionData $action)
    {
        if (!$action instanceof Action) {
            $action = Action::createFrom($action);
        }

        $this->transactionManager->execute(function () use ($challenge, $action) {
            $this->actionsService->saveAction($action);
            $challenge->onSubmit = $action;
            $this->challengeRepository->persist($challenge);
        });
    }
}
