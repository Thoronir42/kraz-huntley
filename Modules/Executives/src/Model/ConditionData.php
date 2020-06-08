<?php declare(strict_types=1);

namespace SeStep\Executives\Model;

interface ConditionData
{
    /** @return string registered condition type */
    public function getType(): string;

    /** @return array condition params */
    public function getParams(): array;
}
