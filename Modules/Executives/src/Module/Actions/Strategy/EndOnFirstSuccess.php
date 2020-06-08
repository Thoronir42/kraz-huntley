<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Actions\Strategy;

use SeStep\Executives\Execution\ExecutionResult;

/**
 * Provides action execution in disjunctive fashion
 *
 * Executor, using this strategy, will execute actions one by one if previous
 * action didn't succeed.
 *
 * Execution stops successfully on first success and if there are no more
 * actions, overall execution results in failure.
 */
final class EndOnFirstSuccess implements MultiActionStrategy
{
    public function onPartialResult(ExecutionResult $result, array $intermediateResults): ?ExecutionResult
    {
        if ($result->isOk()) {
            return ExecutionResult::ok([
                'actionResults' => $intermediateResults,
            ]);
        }

        return null;
    }

    public function onAllActionsDone(array $results): ExecutionResult
    {
        return new ExecutionResult(ExecutionResult::CODE_EXECUTION_FAILED, [
            'actionResults' => $results,
            'reason' => 'noActionExecuted',
        ]);
    }
}
