<?php declare(strict_types=1);

namespace SeStep\Executives\Model\Service;

use App\LeanMapper\TransactionManager;
use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use Nette\NotImplementedException;
use SeStep\Executives\ActionType;
use SeStep\Executives\Exceptions\ActionNotFoundException;
use SeStep\Executives\Exceptions\InvalidActionException;
use SeStep\Executives\ExecutivesModule;
use SeStep\Executives\Model\Entity\Action;
use SeStep\Executives\Model\Entity\Condition;
use SeStep\Executives\Model\Entity\Script;
use SeStep\Executives\Model\Repository\ActionRepository;
use SeStep\Executives\Model\Repository\ConditionRepository;
use SeStep\Executives\Model\Repository\ScriptRepository;

class ActionsService
{
    /** @var ActionRepository */
    private $actionRepository;
    /** @var ConditionRepository */
    private $conditionRepository;
    /** @var ScriptRepository */
    private $scriptRepository;
    /** @var TransactionManager */
    private $transactionManager;

    /** @var ExecutivesModule[] */
    private $modules;
    /** @var Cache */
    private $typesCache;

    /**
     * @param ActionRepository $actionRepository
     * @param ConditionRepository $conditionRepository
     * @param ScriptRepository $scriptRepository
     * @param TransactionManager $transactionManager
     * @param ExecutivesModule[] $modules
     */
    public function __construct(
        ActionRepository $actionRepository,
        ConditionRepository $conditionRepository,
        ScriptRepository $scriptRepository,
        TransactionManager $transactionManager,
        array $modules
    ) {
        $this->actionRepository = $actionRepository;
        $this->conditionRepository = $conditionRepository;
        $this->scriptRepository = $scriptRepository;
        $this->transactionManager = $transactionManager;
        $this->modules = $modules;

        $this->typesCache = new Cache(new MemoryStorage());
    }

    public function getActionTypes()
    {
        return $this->typesCache->load('actionTypes', function () {
            $types = [];
            foreach ($this->modules as $moduleName => $module) {
                foreach ($module->getActionTypes() as $type) {
                    $action = "$moduleName.$type";
                    $types[$action] = $this->getActionLocalisationPlaceholder($action);
                }
            }

            return $types;
        });
    }

    public function getConditionTypes()
    {
        return $this->typesCache->load('conditionTypes', function () {
            $types = [];
            foreach ($this->modules as $moduleName => $module) {
                foreach ($module->getConditionTypes() as $type) {
                    $condition = "$moduleName.$type";
                    $types[$condition] = $this->getConditionLocalisationPlaceholder($condition);
                }
            }

            return $types;
        });
    }

    public function getActionsDataSource(Script $script)
    {
        return $this->actionRepository->getEntityDataSource(['script' => $script]);
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


    public function executeAction(string $type, array $params)
    {
        throw new NotImplementedException();
        $action = $this->getCallable($type);
        if ($action instanceof ActionType) {
            $result = $action->execute($params);
        } else {
            $result = call_user_func_array($action, [$params]);
        }
    }

    private function getCallable(string $type)
    {
        if (!$this->hasActionType($type)) {
            throw new ActionNotFoundException($type, self::getActionTypes());
        }

        $action = self::ACTIONS[$type];
        if (is_string($action) && is_a($action, ActionType::class, true)) {
            return $this->container->getByType($action);
        }
        if (is_callable($action)) {
            return $action;
        }

        throw new InvalidActionException("Action '$type'");
    }

    public function createScript(string $method): Script
    {
        $script = new Script();
        $script->method = $method;
        $this->scriptRepository->persist($script);

        return $script;
    }

    public function getActionLocalisationPlaceholder(string $name)
    {
        return $this->getLocalisationPlaceholder($name, 'action');
    }

    public function getConditionLocalisationPlaceholder(string $name)
    {
        return $this->getLocalisationPlaceholder($name, 'condition');
    }

    public function getLocalisationPlaceholder(string $name, string $type)
    {
        $pos = mb_strrpos($name, '.');
        return mb_substr($name, 0, $pos)
            . ".executives.$type"
            . mb_substr($name, $pos);
    }

}
