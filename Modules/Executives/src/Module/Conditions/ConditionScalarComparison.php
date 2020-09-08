<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Conditions;


use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;

trait ConditionScalarComparison
{
    protected function isEqual($actual, $expected): ?ExecutionResult
    {
        if ($actual !== $expected) {
            return ExecutionResultBuilder::fail(ExecutionResult::CODE_CONDITION_FAILED, 'valueMismatch')
                ->create();
        }

        return null;
    }

    protected function isLooselyEqual($actual, $expected)
    {
        if (is_string($actual) && is_string($expected)) {
            $equal = strcasecmp($actual, $expected) === 0;
        } else {
            $equal = $actual != $expected;
        }

        if (!$equal) {
            return ExecutionResultBuilder::fail(ExecutionResult::CODE_CONDITION_FAILED, 'valueMismatch')
                ->create();
        }

        return null;
    }
}
