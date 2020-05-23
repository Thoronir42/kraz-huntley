<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Actions;

use PHPUnit\Framework\TestCase;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Model\GenericActionData;
use SeStep\Executives\Test\ExecutiveTestUtils;

class MultiActionTest extends TestCase
{
    /**
     * Tests strategy execution in relation to conditionally failing condition
     *
     * @param int $expectedValue
     * @param int $expectedCode return code of multi action
     * @param int[] $expectedCodes return codes of individual actions
     *
     * @param int $initialValue
     * @param string $strategy
     *
     * @dataProvider dataForMultiActionExecution
     */
    public function testMultiActionExecution(
        int $expectedValue,
        int $expectedCode,
        array $expectedCodes,
        int $initialValue,
        string $strategy
    ) {
        $context = new \stdClass();

        $context->acc = $initialValue;

        $multiAction = new GenericActionData('exe.multiAction', [
            'strategy' => $strategy,
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
        $result = $actionExecutor->execute($multiAction, $context);

        self::assertEquals($expectedCode, $result->getCode());
        self::assertEquals($expectedCodes, $this->getIndividualActionReturnCodes($result));

        $expectedContext = new \stdClass();
        $expectedContext->acc = $expectedValue;

        self::assertEquals($expectedContext, $context);
    }

    public function dataForMultiActionExecution()
    {
        $exeOk = ExecutionResult::CODE_OK;
        $condFail = ExecutionResult::CODE_CONDITION_FAILED;
        $exeFail = ExecutionResult::CODE_EXECUTION_FAILED;

        return [
            [6, $exeOk, [$exeOk, $exeOk, $exeOk], 9, 'executeAll'],
            [4, $exeOk, [$exeOk, $condFail, $exeOk], 2, 'executeAll'],

            [10, $exeOk, [$exeOk], 9, 'returnOnFirstPass'],
            [4, $exeOk, [$exeOk], 3, 'returnOnFirstPass'],

            [6, $exeOk, [$exeOk, $exeOk, $exeOk], 9, 'failOnFirstFail'],
            [5, $exeFail, [$exeOk, $condFail], 4, 'failOnFirstFail'],
        ];
    }

    /**
     * Tests strategy execution and return codes in relation to failing action
     *
     * @param int $expectedValue
     * @param int $expectedCode return code of multi action
     * @param int[] $expectedCodes return codes of individual actions
     *
     * @param string $strategy
     *
     * @dataProvider dataForTestDivisionByZero
     */
    public function testDivisionByZero(int $expectedValue, int $expectedCode, array $expectedCodes, string $strategy)
    {
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
                    'params' => ['left' => 'acc', 'right' => 0],
                ],
                [
                    'type' => 'ar.add',
                    'params' => ['left' => 'acc', 'right' => 1],
                ],
            ],
        ]);

        $context = new \stdClass();

        $context->acc = 5;

        $actionExecutor = ExecutiveTestUtils::createActionExecutor();
        $result = $actionExecutor->execute($multiAction, $context);

        self::assertEquals($expectedCode, $result->getCode());
        self::assertEquals($expectedCodes, $this->getIndividualActionReturnCodes($result));

        $expectedContext = new \stdClass();
        $expectedContext->acc = $expectedValue;

        self::assertEquals($expectedContext, $context);
    }

    public function dataForTestDivisionByZero()
    {
        $exeOk = ExecutionResult::CODE_OK;
        $exeErr = ExecutionResult::CODE_EXECUTION_FAILED;
        return [
            [7, $exeOk, [$exeOk, $exeErr, $exeOk], 'executeAll'],
            [6, $exeOk, [$exeOk], 'returnOnFirstPass'],
            [6, $exeErr, [$exeOk, $exeErr], 'failOnFirstFail'],
        ];
    }

    private function getIndividualActionReturnCodes(ExecutionResult $multiActionResult)
    {
        /** @var ExecutionResult[] $actionResults */
        $actionResults = $multiActionResult->getData()['actionResults'];
        return array_map(function ($result) {
            return $result->getCode();
        }, $actionResults);
    }
}
