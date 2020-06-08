<?php declare(strict_types=1);

namespace SeStep\Executives\Execution;

use SeStep\Executives\Model\ActionData;
use SeStep\Executives\ModuleAggregator;

class ActionExecutor
{
    /** @var ModuleAggregator */
    private $moduleAggregator;
    /** @var ExecutivesLocator */
    private $locator;

    public function __construct(ModuleAggregator $moduleAggregator, ExecutivesLocator $locator)
    {
        $this->moduleAggregator = $moduleAggregator;
        $this->locator = $locator;
    }

    public function execute(ActionData $actionData, $context): ExecutionResult
    {
        if (!$this->evaluateConditions($actionData->getConditions(), $context)) {
            return new ExecutionResult(ExecutionResult::CODE_CONDITION_FAILED, []);
        }

        $action = $this->locator->getAction($actionData->getType());

        $result = $action->execute($context, $actionData->getParams());
        $data = $result->getData();

        if (isset($data['update'])) {
            foreach ($data['update'] as $key => $value) {
                $context->$key = $value;
            }
        }

        return $result;
    }

    private function evaluateConditions(array $conditions, $context): bool
    {
        foreach ($conditions as $i => $conditionData) {
            $condition = $this->locator->getCondition($conditionData->getType());

            $conditionResult = $condition->evaluate($context, $conditionData->getParams());
            if ($conditionResult) {
                return false;
            }
        }

        return true;
    }
}
