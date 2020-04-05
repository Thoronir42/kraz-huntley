<?php declare(strict_types=1);

namespace SeStep\Executives;

interface ConditionType
{
    public function evaluate($params, $context): bool;
}
