<?php declare(strict_types=1);

namespace SeStep\Typeful\Service;

use SeStep\Typeful\Types\PropertyType;

class TypeRegistry
{
    /** @var PropertyType[] */
    private $propertyTypes;

    /**
     * TypeRegister constructor.
     *
     * @param PropertyType[] $propertyTypes
     */
    public function __construct(array $propertyTypes)
    {
        foreach ($propertyTypes as $type) {
            $this->propertyTypes[$type::getName()] = $type;
        }
    }

    public function hasType(string $type): bool
    {
        return isset($this->propertyTypes[$type]);
    }

    public function getType(string $type): ?PropertyType
    {
        if (!isset($this->propertyTypes[$type])) {
            trigger_error("Property type '$type' is not recognized");
            return null;
        }

        return $this->propertyTypes[$type];
    }
}
