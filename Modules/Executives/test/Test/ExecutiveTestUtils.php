<?php declare(strict_types=1);

namespace SeStep\Executives\Test;

use Nette\DI\Container;
use SeStep\Executives\ExecutivesLocalization;
use SeStep\Executives\Module\Actions\MultiAction;
use SeStep\Executives\Module\ExecutivesModule;
use SeStep\Executives\Module\MultiActionStrategyFactory;
use SeStep\Executives\ModuleAggregator;
use SeStep\Executives\Execution\ActionExecutor;
use SeStep\Executives\Execution\ExecutivesLocator;

class ExecutiveTestUtils
{
    public static function createTestAggregator(): ModuleAggregator
    {
        return new ModuleAggregator([
            'exe' => new ExecutivesModule(),
            'ar' => new ArithmeticsTestModule(),
            'geo' => new GeometryTestModule(),
        ], new ExecutivesLocalization());
    }

    public static function createActionExecutor(): ActionExecutor
    {
        $moduleAggregator = ExecutiveTestUtils::createTestAggregator();
        $container = new Container();
        $actionExecutor = null;
        $resolveClosure = \Closure::fromCallable(function($class) use ($container, &$actionExecutor) {
            $args = [];
            if(is_string($class) && is_a($class, MultiAction::class, true)) {
                $args['actionExecutor'] = $actionExecutor;
                $args['strategyFactory'] = new MultiActionStrategyFactory();
            }
            return $container->createInstance($class, $args);
        });

        $locator = new ExecutivesLocator($moduleAggregator, $resolveClosure);

        return $actionExecutor = new ActionExecutor($moduleAggregator, $locator);
    }
}
