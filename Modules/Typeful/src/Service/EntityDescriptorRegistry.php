<?php declare(strict_types=1);

namespace SeStep\Typeful\Service;

use Nette\InvalidStateException;
use SeStep\Typeful\Entity;

class EntityDescriptorRegistry
{
    /** @var Entity\EntityDescriptor[] */
    private $descriptors = [];

    public function __construct(array $descriptors)
    {
        foreach ($descriptors as $descriptor) {
            $this->add($descriptor);
        }
    }

    public function getEntityDescriptor(string $entityName): ?Entity\EntityDescriptor
    {
        return $this->descriptors[$entityName] ?? null;
    }

    public function getEntityProperty(string $entityName, string $propertyName): ?Entity\Property
    {
        $descriptor = $this->getEntityDescriptor($entityName);
        if (!$descriptor) {
            throw new InvalidStateException("Entity '$entityName' is not registered");
        }

        return $descriptor->getProperty($propertyName);
    }

    /**
     * @return Entity\EntityDescriptor[]
     */
    public function getDescriptors(): array
    {
        return $this->descriptors;
    }

    private function add(Entity\EntityDescriptor $descriptor): void
    {
        $entityName = $descriptor->getName();
        if (isset($this->descriptors[$entityName])) {
            throw new InvalidStateException("Entity descriptor '$entityName' is already registered");
        }

        $this->descriptors[$entityName] = $descriptor;
    }
}
