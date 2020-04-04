<?php declare(strict_types=1);

namespace SeStep\Typeful\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use SeStep\Typeful\Forms;
use SeStep\Typeful\Service;

class TypefulExtension extends CompilerExtension
{
    const TAG_TYPE = 'typeful.propertyType';
    const TAG_ENTITY = 'typeful.entity';

    private $configFile;

    public function loadConfiguration()
    {
        $this->configFile = $this->loadFromFile(__DIR__ . '/typefulExtension.neon');

        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('typeRegister'))
            ->setType(Service\TypeRegistry::class);

        $builder->addDefinition($this->prefix('entityDescriptorRegister'))
            ->setType(Service\EntityDescriptorRegistry::class);

        $builder->addDefinition($this->prefix('propertyControlFactory'))
            ->setType(Forms\PropertyControlFactory::class);
        $builder->addDefinition($this->prefix('formPopulator'))
            ->setType(Forms\EntityFormPopulator::class);
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $types = $this->configFile['defaultTypes'];

        foreach (array_keys($builder->findByTag(self::TAG_TYPE)) as $typeServiceName) {
            $types[] = $builder->getDefinition($typeServiceName);
        }

        /** @var ServiceDefinition $typeRegister */
        $typeRegister = $builder->getDefinition($this->prefix('typeRegister'));
        $typeRegister->setArgument('propertyTypes', $types);

        $propertyControlFactory = $builder->getDefinition($this->prefix('propertyControlFactory'));
        $propertyControlFactory->setArgument('typeMap', $this->configFile['defaultTypeControlFactories']);

        $descriptors = [];
        foreach ($builder->findByTag(self::TAG_ENTITY) as $service => $entityClass) {
            $descriptors[$entityClass] = $builder->getDefinition($service);
        }

        /** @var ServiceDefinition $entityDescriptorRegister */
        $entityDescriptorRegister = $builder->getDefinition($this->prefix('entityDescriptorRegister'));
        $entityDescriptorRegister->setArgument('descriptors', $descriptors);
    }
}
