<?php

namespace SeStep\Executives\Validation;

use PHPUnit\Framework\TestCase;
use SeStep\Executives\Execution\ExecutivesLocator;
use SeStep\Executives\Test\ExecutiveTestUtils;

class ExecutivesValidatorTest extends TestCase
{
    private function createValidator()
    {
        $locator = new ExecutivesLocator(ExecutiveTestUtils::createTestAggregator(),
            \Closure::fromCallable(function ($resolvedClass) {
                return new $resolvedClass();
            }));

        return new ExecutivesValidator($locator);
    }

    /**
     * @param string $type
     * @param array $params
     * @param string[] $expectedErrorCodes
     *
     * @dataProvider dataForTestValidateAction
     */
    public function testValidateAction(string $type, array $params, array $expectedErrorCodes)
    {
        $validator = $this->createValidator();

        $errors = $validator->validateActionParams($type, $params);

        ExecutiveTestUtils::assertErrorCodes($expectedErrorCodes, $errors);

        $errors = $validator->validateActionData([
            'type' => $type,
            'params' => $params,
        ]);

        ExecutiveTestUtils::assertErrorCodes($expectedErrorCodes, $errors);
    }

    public function dataForTestValidateAction()
    {
        return [
            'pass' => ['ar.add', ['left' => 42, 'right' => 'reference'], []],
            'error type' => ['invalid-action', [], ['type' => 'exe.validation.unknownValue']],
            'error schema' => [
                'ar.divide',
                ['left' => 42, 'rihgt' => 2],
                [ // typo in 'right'
                    'rihgt' => 'schema.structure.unexpectedKey',
                    'right' => 'schema.optionMissing',
                ]
            ],
            'error validation' => [
                'ar.divide',
                ['left' => 42, 'right' => 0],
                [
                    'right' => 'divisionByZero',
                ]
            ],
            'error schema and validation' => [
                'ar.divide',
                ['right' => 0],
                [
                    'left' => 'schema.optionMissing',
                ],
            ]
        ];
    }

    public function testWithPath()
    {
        $validator = $this->createValidator()->withPath('nested.computation');

        $type = 'ar.add';
        $params = [];
        $errors = $validator->validateActionParams($type, $params);


        ExecutiveTestUtils::assertErrorCodes([
            'nested.computation.left' => 'schema.optionMissing',
            'nested.computation.right' => 'schema.optionMissing',
        ], $errors);

    }
}
