<?php declare(strict_types=1);

namespace SeStep\NetteTypeful\Components;

use SeStep\Typeful\Service\EntityDescriptorRegistry;
use Ublaboo\DataGrid\DataGrid;

class EntityGridFactory
{
    /** @var EntityDescriptorRegistry */
    private $entityDescriptorRegistry;

    public function __construct(EntityDescriptorRegistry $entityDescriptorRegistry)
    {
        $this->entityDescriptorRegistry = $entityDescriptorRegistry;
    }

    public function create(string $entityName, array $properties = []): DataGrid
    {
        $entity = $this->entityDescriptorRegistry->getEntityDescriptor($entityName, true);
        $entityProperties = $entity->getProperties();
        if ($properties) {
            $entityProperties = array_intersect_key($entityProperties, array_flip($properties));
        }

        $grid = new DataGrid();
        foreach ($entityProperties as $name => $property) {
            $grid->addColumnText($name, $name);
        }

        return $grid;
    }
}
