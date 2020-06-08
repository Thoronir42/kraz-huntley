<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Actions;

use SeStep\Executives\Module\MultiActionStrategyFactory;
use SeStep\Executives\Model\GenericActionData;
use SeStep\Executives\Execution\ActionExecutor;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;

class MultiAction implements Action
{
    /** @var MultiActionStrategyFactory */
    private $strategyFactory;
    /** @var ActionExecutor */
    private $actionExecutor;

    public function __construct(MultiActionStrategyFactory $strategyFactory, ActionExecutor $actionExecutor)
    {
        $this->strategyFactory = $strategyFactory;
        $this->actionExecutor = $actionExecutor;
    }

    public function execute($context, $params): ExecutionResult
    {
        $strategy = $this->strategyFactory->create($params['strategy']);

        $actionResults = [];

        foreach (GenericActionData::createManyFrom($params['actions']) as $i => $actionData) {
            $result = $this->actionExecutor->execute($actionData, $context);
            $actionResults[$i] = $result;

            $strategyResult = $strategy->onPartialResult($result, $actionResults);
            if ($strategyResult) {
                return $strategyResult;
            }
        }

        return $strategy->onAllActionsDone($actionResults);
    }
}
