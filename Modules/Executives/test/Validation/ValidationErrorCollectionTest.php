<?php declare(strict_types=1);

namespace SeStep\Executives\Validation;

use PHPUnit\Framework\TestCase;

class ValidationErrorCollectionTest extends TestCase
{
    public function testFlatStructure()
    {
        $collection = new ValidationErrorCollection([
            'chassis' => new ParamValidationError('materialError', ['gold']),
            'wheels' => new ParamValidationError('invalidComponentCount', [7]),
        ]);

        $errors = [];
        foreach ($collection as $field => $error) {
            $errors[$field][] = [$error->getErrorType(), $error->getErrorData()];
        }

        self::assertEquals([
            'chassis' => [['materialError', ['gold']]],
            'wheels' => [['invalidComponentCount', [7]]],
        ], $errors);
    }

    public function testNestedStructure()
    {
        $collection = new ValidationErrorCollection([
            'windshield' => [
                new ParamValidationError('invalidSizing', [800, 600]),
                new ParamValidationError('invalidComponentCount', [3]),
            ],
        ]);

        $errors = [];
        foreach ($collection as $field => $error) {
            $errors[$field][] = [$error->getErrorType(), $error->getErrorData()];
        }

        self::assertEquals([
            'windshield' => [
                ['invalidSizing', [800, 600]],
                ['invalidComponentCount', [3]],
            ],
        ], $errors);
    }

    public function testMultipleRuns()
    {
        $collection = new ValidationErrorCollection([
            'chassis' => new ParamValidationError('materialError', ['gold']),
            'wheels' => new ParamValidationError('invalidComponentCount', [7]),
        ]);

        $expected = [
            'chassis' => [['materialError', ['gold']]],
            'wheels' => [['invalidComponentCount', [7]]],
        ];
        for ($i = 0; $i < 2; $i++) {
            $errors = [];
            foreach ($collection as $field => $error) {
                $errors[$field][] = [$error->getErrorType(), $error->getErrorData()];
            }
            self::assertEquals($expected, $errors);
        }

    }
}
