<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Conditions;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Condition;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Module\Conditions\ConditionScalarComparison;
use SeStep\Executives\Validation\HasParamsSchema;

class AnswerCorrect implements Condition, HasParamsSchema
{
    use ConditionScalarComparison;

    public function evaluate($context, $params): ?ExecutionResult
    {
        return $this->isLooselyEqual($context->answer, $context->challenge->correctAnswer);
    }

    public function getParamsSchema(): Schema
    {
        return Expect::structure([]);
    }
}
