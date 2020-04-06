<?php declare(strict_types=1);

namespace SeStep\Typeful\Service;

use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use SeStep\Typeful\Types\PropertyType;

class TypeRegistry
{
    /** @var PropertyType[] */
    private $propertyTypes;

    /** @var Cache */
    private $cache;

    /**
     * TypeRegister constructor.
     *
     * @param PropertyType[] $propertyTypes
     */
    public function __construct(array $propertyTypes)
    {
        $this->propertyTypes = $propertyTypes;

        $this->cache = new Cache(new MemoryStorage());
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

    public function getTypesLocalized()
    {
        return $this->cache->load('typesLocalized', function () {
            $types = array_keys($this->propertyTypes);

            return array_combine($types, $types);
        });
    }
}
