<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Conditions;

use Nette\NotImplementedException;
use SeStep\Executives\ConditionType;

class AnswerEquals implements ConditionType
{
    public function evaluate($params, $context): bool
    {
        throw new NotImplementedException();

    }
}
