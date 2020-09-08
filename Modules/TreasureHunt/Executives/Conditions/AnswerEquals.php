<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Conditions;

use Nette\NotImplementedException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Condition;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;
use SeStep\Executives\Module\Conditions\ConditionScalarComparison;
use SeStep\Executives\Validation\HasParamsSchema;

class AnswerEquals implements Condition, HasParamsSchema
{
    use ConditionScalarComparison;

    public function evaluate($context, $params): ?ExecutionResult
    {
        return $this->isLooselyEqual($context->answer, $params['value']);
    }

    public function getParamsSchema(): Schema
    {
        return Expect::structure([
            'value' => Expect::mixed()->required(),
        ]);
    }
}
