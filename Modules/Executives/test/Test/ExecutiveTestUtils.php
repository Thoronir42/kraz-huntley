<?php declare(strict_types=1);

namespace SeStep\Executives\Test;

use Nette\DI\Container;
use PHPUnit\Framework\Assert;
use SeStep\Executives\ExecutivesLocalization;
use SeStep\Executives\Module\Actions\MultiAction;
use SeStep\Executives\Module\ExecutivesModule;
use SeStep\Executives\Module\MultiActionStrategyFactory;
use SeStep\Executives\ModuleAggregator;
use SeStep\Executives\Execution\ActionExecutor;
use SeStep\Executives\Execution\ExecutivesLocator;
use SeStep\Executives\Validation\ParamValidationError;
use SeStep\Executives\Validation\ValidationErrorCollection;

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
        $resolveClosure = \Closure::fromCallable(function ($class) use ($container, &$actionExecutor) {
            $args = [];
            if (is_string($class) && is_a($class, MultiAction::class, true)) {
                $args['actionExecutor'] = $actionExecutor;
                $args['strategyFactory'] = new MultiActionStrategyFactory();
            }
            return $container->createInstance($class, $args);
        });

        $locator = new ExecutivesLocator($moduleAggregator, $resolveClosure);

        return $actionExecutor = new ActionExecutor($moduleAggregator, $locator);
    }

    /**
     * @param string[] $expectedCodes
     * @param ParamValidationError[]|ValidationErrorCollection $errors
     */
    public static function assertErrorCodes(array $expectedCodes, $errors, string $message = '')
    {
        if ($errors instanceof ValidationErrorCollection) {
            $errors = iterator_to_array($errors);
        }

        $actualCodes = [];
        foreach ($errors as $field => $error) {
            $actualCodes[$field] = $error->getErrorType();
        }

        Assert::assertEquals($expectedCodes, $actualCodes, $message);
    }
}
