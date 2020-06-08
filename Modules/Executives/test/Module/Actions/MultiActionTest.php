<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Actions;

use PHPUnit\Framework\TestCase;
use SeStep\Executives\Model\GenericActionData;
use SeStep\Executives\Test\ExecutiveTestUtils;

class MultiActionTest extends TestCase
{
    /**
     * @param int $expectedResult
     *
     * @param int $initialValue
     * @param string $strategy
     *
     * @dataProvider dataForMultiActionExecution
     */
    public function testMultiActionExecution(int $expectedResult, int $initialValue, string $strategy)
    {
        $context = new \stdClass();

        $context->acc = $initialValue;

        $multiAction = new GenericActionData('exe.multiAction', [
            'strategy' => $strategy,
            'update' => 'acc',
            'actions' => [
                [
                    'type' => 'ar.add',
                    'params' => ['left' => 'acc', 'right' => 1],
                ],
                [
                    'type' => 'ar.divide',
                    'params' => ['left' => 'acc', 'right' => 2],
                    'conditions' => [
                        ['type' => 'ar.parity', 'params' => ['field' => 'acc', 'type' => 'even']],
                    ],
                ],
                [
                    'type' => 'ar.add',
                    'params' => ['left' => 'acc', 'right' => 1],
                ],
            ],
        ]);

        $actionExecutor = ExecutiveTestUtils::createActionExecutor();
        $actionExecutor->execute($multiAction, $context);

        $expectedContext = new \stdClass();
        $expectedContext->acc = $expectedResult;

        self::assertEquals($expectedContext, $context);
    }

    public function dataForMultiActionExecution()
    {
        return [
            [6, 9, 'executeAll'],
            [4, 2, 'executeAll'],

            [10, 9, 'returnOnFirstPass'],
            [4, 3, 'returnOnFirstPass'],

            [6, 9, 'failOnFirstFail'],
            [5, 4, 'failOnFirstFail'],
        ];
    }
}
