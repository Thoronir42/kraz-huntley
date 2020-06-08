<?php declare(strict_types=1);

namespace SeStep\Executives;

use PHPUnit\Framework\TestCase;
use SeStep\Executives\Module\Actions\MultiAction;
use SeStep\Executives\Test as TestModule;
use SeStep\Executives\Test\ExecutiveTestUtils;

class ModuleAggregatorTest extends TestCase
{
    public function testGetActions()
    {
        $aggregator = ExecutiveTestUtils::createTestAggregator();

        $expectedActions = [
            'ar.add' => TestModule\Arithmetics\Add::class,
            'ar.divide' => TestModule\Arithmetics\Divide::class,
            'geo.squareSurface' => TestModule\Geometry\SquareSurfaceAction::class,
            'exe.multiAction' => MultiAction::class,
        ];
        self::assertEquals($expectedActions, $aggregator->getActions());
    }

    public function testGetActionsPlaceholders()
    {
        $aggregator = ExecutiveTestUtils::createTestAggregator();

        $expectedActions = [
            'ar.add' => 'arithmetics.executives.action.add',
            'ar.divide' => 'arithmetics.executives.action.divide',
            'geo.squareSurface' => 'geo.executives.action.squareSurface',
            'exe.multiAction' => 'exe.executives.action.multiAction',
        ];
        self::assertEquals($expectedActions, $aggregator->getActionsPlaceholders());
    }
}
