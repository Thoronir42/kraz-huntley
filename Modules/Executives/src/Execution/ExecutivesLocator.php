<?php declare(strict_types=1);

namespace SeStep\Executives\Execution;

use Closure;
use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use SeStep\Executives\ModuleAggregator;

/**
 * Provides instances of Actions and Conditions
 *
 * @internal
 */
class ExecutivesLocator
{
    /** @var ModuleAggregator */
    private $moduleAggregator;
    /** @var Closure */
    private $resolveClosure;

    private $cache;

    /**
     * ExecutivesLocator constructor.
     *
     * @param ModuleAggregator $moduleAggregator
     * @param Closure $resolveClosure takes single parameter - the required className
     */
    public function __construct(ModuleAggregator $moduleAggregator, Closure $resolveClosure)
    {
        $this->moduleAggregator = $moduleAggregator;
        $this->resolveClosure = $resolveClosure;

        $this->cache = new Cache(new MemoryStorage());
    }

    public function getAction(string $type): Action
    {
        return $this->cache->load("action.$type", function () use ($type) {
            $class = $this->moduleAggregator->getAction($type);
            return call_user_func($this->resolveClosure, $class);
        });
    }

    public function getCondition(string $type): Condition
    {
        return $this->cache->load("condition.$type", function () use ($type) {
            $class = $this->moduleAggregator->getCondition($type);
            return call_user_func($this->resolveClosure, $class);
        });
    }
}
