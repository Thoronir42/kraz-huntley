<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Actions\Strategy;

use SeStep\Executives\Execution\ExecutionResult;

/**
 * Provides action execution in conjunctive fashion
 *
 * Executor, using this strategy, will execute actions one by one only if
 * previous action succeeded.
 *
 * Execution fails on first unsuccessful action result and if there are no more
 * actions, overall execution results in success.
 */
class ExecuteUntilFirstFail implements MultiActionStrategy
{
    public function onPartialResult(ExecutionResult $result, array $intermediateResults): ?ExecutionResult
    {
        if (!$result->isOk()) {
            return new ExecutionResult(ExecutionResult::CODE_EXECUTION_FAILED, [
                'actionResults' => $intermediateResults,
            ]);
        }

        return null;
    }

    public function onAllActionsDone(array $results): ExecutionResult
    {
        return ExecutionResult::ok([
            'actionResults' => $results,
        ]);
    }
}
