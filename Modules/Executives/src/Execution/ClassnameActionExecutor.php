<?php declare(strict_types=1);

namespace SeStep\Executives\Execution;

use Closure;
use Nette\InvalidStateException;

class ClassnameActionExecutor
{
    /** @var Closure */
    private $resolveByClassname;

    public function __construct(Closure $resolveByClassname)
    {
        $this->resolveByClassname = $resolveByClassname;
    }

    public function execute(string $className, array $params, $context): ExecutionResult
    {
        $action = call_user_func($this->resolveByClassname, $className);
        if (!$action instanceof Action) {
            throw new InvalidStateException("Classname $className did not resolve as instance of " . Action::class);
        }

        return $action->execute($context, $params);
    }
}
