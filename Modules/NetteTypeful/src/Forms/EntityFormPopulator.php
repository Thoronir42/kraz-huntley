<?php declare(strict_types=1);

namespace SeStep\NetteTypeful\Forms;

use Nette\Forms\Form;
use SeStep\Typeful\Entity\EntityDescriptor;
use SeStep\Typeful\Entity\Property;
use SeStep\Typeful\Service\EntityDescriptorRegistry;

class EntityFormPopulator
{
    /** @var EntityDescriptorRegistry */
    private $entityDescriptorRegistry;
    /** @var PropertyControlFactory */
    private $propertyControlFactory;

    public function __construct(
        EntityDescriptorRegistry $entityDescriptorRegistry,
        PropertyControlFactory $propertyControlFactory
    ) {
        $this->entityDescriptorRegistry = $entityDescriptorRegistry;
        $this->propertyControlFactory = $propertyControlFactory;
    }

    public function fillFromReflection(Form $form, string $entityName, array $properties = [])
    {
        $descriptor = $this->entityDescriptorRegistry->getEntityDescriptor($entityName);

        foreach ($this->retrieveProperties($descriptor, $properties) as $name => $property) {
            $label = $descriptor->getPropertyFullName($name);
            $form[$name] = $this->propertyControlFactory->createByProperty($label, $property);
        }
    }

    /**
     * @param EntityDescriptor $descriptor
     * @param string[] $properties
     *
     * @return Property[]
     */
    private function retrieveProperties(EntityDescriptor $descriptor, array $properties): array
    {
        $reflectionProperties = $descriptor->getProperties();
        if (empty($properties)) {
            return $reflectionProperties;
        }

        $desiredProperties = array_flip($properties);
        $missing = array_diff_key($desiredProperties, $reflectionProperties);
        if (!empty($missing)) {
            throw new \InvalidArgumentException("Following properties are not included in descriptor: ["
                . implode(', ', array_flip($missing)) . "]");
        }


        $result = [];
        foreach ($properties as $property) {
            $result[$property] = $reflectionProperties[$property];
        }

        return $result;
    }
}
