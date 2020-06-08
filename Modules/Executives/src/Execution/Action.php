<?php declare(strict_types=1);

namespace SeStep\Executives\Execution;

interface Action
{
    public function execute($context, $params): ExecutionResult;
}
