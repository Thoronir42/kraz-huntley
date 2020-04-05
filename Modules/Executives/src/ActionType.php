<?php declare(strict_types=1);

namespace SeStep\Executives;

interface ActionType
{
    public function execute(array $params);
}
