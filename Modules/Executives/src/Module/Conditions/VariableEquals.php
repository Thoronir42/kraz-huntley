<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Conditions;


use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Condition;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Validation\HasParamsSchema;

class VariableEquals implements Condition, HasParamsSchema
{
    use ConditionScalarComparison;

    public function evaluate($context, $params): ?ExecutionResult
    {
        $actual = $context->{$params['variable']} ?? null;
        $expected = $params['value'];

        return $this->isEqual($actual, $expected);
    }

    public function getParamsSchema(): Schema
    {
        return Expect::structure([
            'variable' => Expect::string()->required(),
            'value' => Expect::mixed()->required(),
        ]);
    }
}
