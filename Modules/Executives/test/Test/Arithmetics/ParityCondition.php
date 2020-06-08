<?php declare(strict_types=1);

namespace SeStep\Executives\Test\Arithmetics;

use SeStep\Executives\Execution\Condition;
use SeStep\Executives\Execution\ExecutionResult;

class ParityCondition implements Condition
{

    public function evaluate($context, $params): ?ExecutionResult
    {
        $field = $params['field'];
        $type = $params['parity'] ?? 'even';

        $expectedModulo = $type === 'even' ? 0 : 1;

        if (!isset($context->$field)) {
            return new ExecutionResult(ExecutionResult::CODE_CONDITION_FAILED, [
                'reason' => 'missing field',
                'field' => $field,
            ]);
        }
        $value = $context->$field;
        if (!is_int($value)) {
            return new ExecutionResult(ExecutionResult::CODE_CONDITION_FAILED, [
                'reason' => 'invalid type',
                'expectedType' => 'int',
                'value' => $value,
            ]);
        }

        $moduloClass = $value % 2;

        if ($moduloClass !== $expectedModulo) {
            return new ExecutionResult(ExecutionResult::CODE_CONDITION_FAILED, [
                'reason' => 'wrong parity class',
                'expectedClass' => $expectedModulo,
                'actualClass' => $moduloClass,
            ]);
        }

        return null;
    }
}
