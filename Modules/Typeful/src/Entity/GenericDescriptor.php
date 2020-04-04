<?php declare(strict_types=1);

namespace SeStep\Typeful\Entity;


class GenericDescriptor implements EntityDescriptor
{

    /** @var string */
    private $name;
    /** @var Property[] */
    private $properties = [];
    /** @var string */
    private $propertyNamePrefix;

    /**
     * GenericDescriptor constructor.
     *
     * @param string $name
     * @param Property[] $properties list of properties
     * @param string $propertyNamePrefix
     */
    public function __construct(string $name, array $properties, string $propertyNamePrefix = '')
    {
        $this->name = $name;
        $this->propertyNamePrefix = $propertyNamePrefix;
        foreach ($properties as $property) {
            $name = $property->getName();
            if (isset($this->properties[$name])) {
                throw new \InvalidArgumentException("Can not redefine property '$name'");
            }
            $this->properties[$name] = $property;
        }
    }

    /** * @inheritDoc */
    public function getName(): string
    {
        return $this->name;
    }

    /** @inheritDoc */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /** @inheritDoc */
    public function getProperty(string $name): ?Property
    {
        return $this->properties[$name] ?? null;
    }

    /** @inheritDoc */
    public function getPropertyFullName(string $name): ?string
    {
        if (!($property = $this->properties[$name] ?? null)) {
            return null;
        }

        if (!$this->propertyNamePrefix) {
            return $name;
        }

        return "$this->propertyNamePrefix.$name";
    }
}
