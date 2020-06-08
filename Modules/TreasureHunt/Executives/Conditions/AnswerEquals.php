<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Conditions;

use Nette\NotImplementedException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Condition;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;
use SeStep\Executives\Validation\HasParamsSchema;

class AnswerEquals implements Condition, HasParamsSchema
{
    public function evaluate($context, $params): ?ExecutionResult
    {
        // todo: try to use === or verify validity of ==
        if ($context->answer != $params['value']) {
            return ExecutionResultBuilder::fail(ExecutionResult::CODE_CONDITION_FAILED, 'valueMismatch')
                ->create();
        }

        return null;
    }

    public function getParamsSchema(): Schema
    {
        return Expect::structure([
            'value' => Expect::mixed()->required(),
        ]);
    }
}
