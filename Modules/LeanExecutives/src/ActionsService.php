<?php declare(strict_types=1);

namespace SeStep\LeanExecutives;

use App\LeanMapper\TransactionManager;
use SeStep\Executives\ExecutivesLocalization;
use SeStep\Executives\ModuleAggregator;
use SeStep\LeanExecutives\Entity\Action;
use SeStep\LeanExecutives\Entity\Condition;
use SeStep\LeanExecutives\Repository\ActionRepository;
use SeStep\LeanExecutives\Repository\ConditionRepository;

class ActionsService
{
    /** @var ActionRepository */
    private $actionRepository;
    /** @var ConditionRepository */
    private $conditionRepository;
    /** @var TransactionManager */
    private $transactionManager;
    /** * @var ExecutivesLocalization */
    private $executivesLocalization;
    /** @var ModuleAggregator */
    private $executivesRegistry;

    /**
     * @param ActionRepository $actionRepository
     * @param ConditionRepository $conditionRepository
     * @param TransactionManager $transactionManager
     * @param ExecutivesLocalization $executivesLocalization
     * @param ModuleAggregator $executivesRegistry
     */
    public function __construct(
        ActionRepository $actionRepository,
        ConditionRepository $conditionRepository,
        TransactionManager $transactionManager,
        ExecutivesLocalization $executivesLocalization,
        ModuleAggregator $executivesRegistry
    ) {
        $this->actionRepository = $actionRepository;
        $this->conditionRepository = $conditionRepository;
        $this->transactionManager = $transactionManager;
        $this->executivesLocalization = $executivesLocalization;
        $this->executivesRegistry = $executivesRegistry;
    }


    public function getAction(string $id): ?Action
    {
        return $this->actionRepository->findOneBy(['id' => $id]);
    }


    /**
     * @param Action $action
     * @param mixed[]|Condition[] $conditions
     */
    public function saveAction(Action $action, array $conditions = [])
    {
        /** @var Condition[] $conditionsEntities */
        $conditionsEntities = $conditions ? array_map(function ($cond) use ($action) {
            if (!$cond instanceof Condition) {
                $cond = new Condition($cond);
            }

            return $cond;
        }, $conditions) : [];

        $this->transactionManager->execute(function () use ($action, $conditionsEntities) {
            if (!$action->isDetached()) {
                $this->actionRepository->deleteConditions($action);
            }

            $this->actionRepository->persist($action);

            $this->conditionRepository->persistMany($conditionsEntities);
            $action->addToConditions($conditionsEntities);
            $this->actionRepository->persist($action);
        });
    }

    public function createActionByClass(string $class, array $params = []): Action
    {
        $action = new Action();
        $action->type = $this->executivesRegistry->getActionTypeByClass($class);
        $action->params = $params;

        $this->actionRepository->persist($action);

        return $action;
    }

}
