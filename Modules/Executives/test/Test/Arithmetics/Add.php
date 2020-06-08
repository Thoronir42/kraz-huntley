<?php declare(strict_types=1);

namespace SeStep\Executives\Test\Arithmetics;


use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;

class Add implements Action
{
    public function execute($context, $params): ExecutionResult
    {
        $left = $params['left'];
        $right = $params['right'];
        $target = $params['target'] ?? $left;

        $leftValue = is_numeric($left) ? $left : $context->$left;
        $rightValue = is_numeric($right) ? $right : $context->$right;

        return ExecutionResult::ok([
            'update' => [
                $target => $leftValue + $rightValue,
            ],
        ]);
    }
}
