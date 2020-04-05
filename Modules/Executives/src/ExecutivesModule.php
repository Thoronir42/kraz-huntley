<?php declare(strict_types=1);

namespace SeStep\Executives;

interface ExecutivesModule
{

    /** @return string[] */
    public function getActionTypes(): array;

    public function getConditionTypes(): array;
}
