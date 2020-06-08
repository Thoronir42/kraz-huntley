<?php declare(strict_types=1);

namespace SeStep\Executives\Test\Arithmetics;


use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;
use SeStep\Executives\Validation\HasParamsSchema;

class Add implements Action, HasParamsSchema
{
    public function execute($context, $params): ExecutionResult
    {
        $left = $params['left'];
        $right = $params['right'];
        $target = $params['target'] ?? $left;

        $leftValue = is_numeric($left) ? $left : $context->$left;
        $rightValue = is_numeric($right) ? $right : $context->$right;

        return ExecutionResultBuilder::ok()
            ->update($target, $leftValue + $rightValue)
            ->create();
    }

    public function getParamsSchema(): Schema
    {
        $referenceOrNumeric = Expect::anyOf(Expect::int(), Expect::float(), Expect::string())->required();
        return Expect::structure([
            'left' => $referenceOrNumeric,
            'right' => $referenceOrNumeric,
            'target' => Expect::string(),
        ]);
    }
}
