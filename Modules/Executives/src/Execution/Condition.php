<?php declare(strict_types=1);

namespace SeStep\Executives\Execution;

use SeStep\Executives\Execution\ExecutionResult;

interface Condition
{
    /**
     * Evaluates $data on $context and if error occurs, returns ExecutionResult
     *
     * @param $context
     * @param $params
     * @return ExecutionResult|null
     */
    public function evaluate($context, $params): ?ExecutionResult;
}
