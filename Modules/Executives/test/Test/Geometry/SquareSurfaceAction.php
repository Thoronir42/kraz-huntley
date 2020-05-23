<?php declare(strict_types=1);

namespace SeStep\Executives\Test\Geometry;

use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;

class SquareSurfaceAction implements Action
{
    public function execute($context, $params): ExecutionResult
    {
        $paramsObj = new SquareSurfaceActionParams($params);

        $contextError = $this->checkContext($context, $paramsObj);
        if ($contextError) {
            return $contextError;
        }

        $a = $context->{$paramsObj->side};

        return ExecutionResultBuilder::ok()
            ->update('surface', $a * $a)
            ->create();
    }

    private function checkContext($context, $data): ?ExecutionResult
    {
        if (!isset($context->a)) {
            return new ExecutionResult(ExecutionResult::CODE_EXECUTION_FAILED, [
                'missing' => 'a',
            ]);
        }

        return null;
    }
}

/** @internal */
class SquareSurfaceActionParams
{
    public $side = 'a';

    public function __construct($params)
    {
        foreach ($this as $key => $_) {
            dump($key);
        }
        dump($params);
        exit;
    }
}
