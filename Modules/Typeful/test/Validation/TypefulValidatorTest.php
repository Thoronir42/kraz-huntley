<?php declare(strict_types=1);

namespace SeStep\Typeful\Validation;

use Nette\InvalidStateException;
use PHPUnit\Framework\TestCase;
use SeStep\Typeful\TestDoubles\RegistryFactory;
use SeStep\Typeful\Types\IntType;

class TypefulValidatorTest extends TestCase
{
    /**
     * @param mixed[] $data
     * @param ValidationError[] $expectedErrors
     *
     * @dataProvider dataForTestValidateExistingEntity
     */
    public function testValidateExistingEntity(array $data, array $expectedErrors)
    {
        $validator = $this->createValidatorInstance();

        $actualErrors = $validator->validateEntity(RegistryFactory::TEST_ENTITY_FURNITURE, $data);

        self::assertCount(count($expectedErrors), $actualErrors);

        self::assertEquals($expectedErrors, $actualErrors);


    }

    public function dataForTestValidateExistingEntity()
    {
        return [
            'valid, all properties' => [
                ['class' => 'chair', 'legCount' => 8, 'description' => 'Spider chair, very comfortable'],
                [],
            ],
            'valid, nullable property' => [
                ['class' => 'table', 'legCount' => 3],
                [],
            ],
            'invalid, missing non-nullable property' => [
                ['class' => 'table'],
                [
                    'legCount' => new ValidationError(ValidationError::UNDEFINED_VALUE),
                ],
            ],
            'invalid, invalid property value' => [
                ['class' => 'wardrobe', 'legCount' => 13],
                [
                    'legCount' => new ValidationError(IntType::ERROR_MORE_THAN_MAX),
                ],
            ],
            'invalid, surplus field' => [
                ['class' => 'table', 'legCount' => 2, 'legType' => 'board'],
                [
                    'legType' => new ValidationError(ValidationError::SURPLUS_FIELD),
                ],
            ],
        ];
    }

    public function testNonExistentEntity()
    {
        $validator = $this->createValidatorInstance();

        $this->expectException(InvalidStateException::class);

        $validator->validateEntity('proverb', [
            'language' => 'en',
            'text' => 'A bad workman always blames his tools',
        ]);
    }

    private static function createValidatorInstance()
    {
        return new TypefulValidator(RegistryFactory::createEntityRegistry(),
            RegistryFactory::createTypeRegistry());
    }
}
