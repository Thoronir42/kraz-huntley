<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Actions\Strategy;

use SeStep\Executives\Execution\ExecutionResult;

/**
 * Executes all actions regardless of their results
 */
class ExecuteAll implements MultiActionStrategy
{
    public function onPartialResult(ExecutionResult $result, array $intermediateResults): ?ExecutionResult
    {
        return null;
    }

    public function onAllActionsDone(array $results): ExecutionResult
    {
        return ExecutionResult::ok([
            'actionResults' => $results,
        ]);
    }
}
