<?php declare(strict_types=1);

namespace SeStep\Executives\Model;

interface ActionData
{
    /** @return string registered action type */
    public function getType(): string;

    /** @return array action parameters */
    public function getParams(): array;

    /** @return ConditionData[] */
    public function getConditions(): array;
}
