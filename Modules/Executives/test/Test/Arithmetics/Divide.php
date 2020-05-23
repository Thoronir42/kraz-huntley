<?php


namespace SeStep\Executives\Test\Arithmetics;


use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;

class Divide implements Action
{
    public function execute($context, $params): ExecutionResult
    {
        $left = $params['left'];
        $right = $params['right'];
        $target = $params['target'] ?? $left;

        $leftValue = is_numeric($left) ? $left : $context->$left;
        $rightValue = is_numeric($right) ? $right : $context->$right;

        if ($rightValue === 0) {
            return ExecutionResultBuilder::fail(ExecutionResult::CODE_EXECUTION_FAILED, 'arr.err.divByZero')
                ->create();
        }

        return ExecutionResultBuilder::ok()
            ->update($target, $leftValue / $rightValue)
            ->create();
    }
}
