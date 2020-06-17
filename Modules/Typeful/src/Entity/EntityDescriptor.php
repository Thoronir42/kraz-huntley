<?php declare(strict_types=1);

namespace SeStep\Typeful\Entity;

/**
 * Entity descriptor holds information about an entity
 */
interface EntityDescriptor
{
    /**
     * Returns associative array of properties of described entity
     *
     * @return Property[]
     */
    public function getProperties(): array;

    /**
     * Retrieves property of given name
     *
     * @param string $name
     *
     * @return Property|null
     */
    public function getProperty(string $name): ?Property;

    /**
     * Returns full name of property
     *
     * @param string $name
     * @return string|null
     */
    public function getPropertyFullName(string $name): ?string;
}
