<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Actions\Strategy;

use SeStep\Executives\Execution\ExecutionResult;

interface MultiActionStrategy
{
    /**
     * When single action has been executed, returns overall result or null if execution should continue
     *
     * @param ExecutionResult $result result of current action executed
     * @param ExecutionResult[] $intermediateResults results of all actions executed so far including $result
     *
     * @return ExecutionResult|null
     */
    public function onPartialResult(ExecutionResult $result, array $intermediateResults): ?ExecutionResult;

    /**
     * When every action has been executed, decide the result
     *
     * @param ExecutionResult[] $results
     * @return ExecutionResult
     */
    public function onAllActionsDone(array $results): ExecutionResult;
}
