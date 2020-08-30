<?php declare(strict_types=1);

namespace SeStep\Typeful\Validation;

use SeStep\Typeful\Entity\TypefulEntity;
use SeStep\Typeful\Service\EntityDescriptorRegistry;
use SeStep\Typeful\Service\TypeRegistry;

class TypefulValidator
{
    /** @var EntityDescriptorRegistry */
    private $entityDescriptorRegistry;
    /** @var TypeRegistry */
    private $typeRegistry;

    public function __construct(EntityDescriptorRegistry $entityDescriptorRegistry, TypeRegistry $typeRegistry)
    {
        $this->entityDescriptorRegistry = $entityDescriptorRegistry;
        $this->typeRegistry = $typeRegistry;
    }

    public function entityExists(string $entityName): bool
    {
        return $this->entityDescriptorRegistry->getEntityDescriptor($entityName) !== null;
    }

    /**
     * @param string $entity
     * @param mixed[]|TypefulEntity $propertyData
     *
     * @return ValidationError[]
     */
    public function validateEntity(string $entity, $propertyData): array
    {
        $descriptor = $this->entityDescriptorRegistry->getEntityDescriptor($entity, true);
        $properties = $descriptor->getProperties();

        // todo: actually use the type TypefulEntity
        if (is_object($propertyData)) {
            $propertyData = $propertyData->getData(array_keys($properties));
        }

        $errors = [];
        if (!empty($surplusProperties = array_diff_key($propertyData, $properties))) {
            foreach (array_keys($surplusProperties) as $property) {
                $errors[$property] = new ValidationError(ValidationError::SURPLUS_FIELD);
            }

            return $errors;
        }

        foreach ($properties as $name => $property) {
            $value = $propertyData[$name] ?? null;
            if ($value === null) {
                if (!$property->isNullable()) {
                    $errors[$name] = new ValidationError(ValidationError::UNDEFINED_VALUE);
                }

                continue;
            }

            if ($error = $this->validateValue($property->getType(), $property->getTypeOptions(), $value)) {
                $errors[$name] = $error;
            }
        }

        return $errors;
    }

    public function validateValue(string $type, array $options, $value): ?ValidationError
    {
        $valueType = $this->typeRegistry->getType($type);

        return $valueType->validateValue($value, $options);
    }
}
