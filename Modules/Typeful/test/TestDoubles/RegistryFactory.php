<?php declare(strict_types=1);

namespace SeStep\Typeful\TestDoubles;

use SeStep\Typeful\Entity\GenericDescriptor;
use SeStep\Typeful\Entity\Property;
use SeStep\Typeful\Service\EntityDescriptorRegistry;

class RegistryFactory
{
    public static function createEntityRegistry(): EntityDescriptorRegistry
    {
        $furnitureDescriptor = new GenericDescriptor('furniture', [
            new Property('class', 'text'),
            new Property('legCount', 'int'),
            new Property('description', 'text', ['richText' => true]),
        ]);

        return new EntityDescriptorRegistry([
            'furniture' => $furnitureDescriptor,
        ]);
    }
}
