<?php


namespace SeStep\Executives\Test\Arithmetics;


use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;
use SeStep\Executives\Validation\HasParamsSchema;
use SeStep\Executives\Validation\ParamValidationError;
use SeStep\Executives\Validation\ValidatesParams;

class Divide implements Action, HasParamsSchema, ValidatesParams
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

    public function getParamsSchema(): Schema
    {
        $referenceOrNumeric = Expect::anyOf(Expect::int(), Expect::float(), Expect::string()->min(1))->required();
        return Expect::structure([
            'left' => $referenceOrNumeric,
            'right' => $referenceOrNumeric,
            'target' => Expect::string()->min(1),
        ]);
    }


    public function validateParams(array $params): array
    {
        $errors = [];

        if ($params['right'] == 0) {
            $errors['right'] = new ParamValidationError('divisionByZero');
        }

        return $errors;
    }
}
