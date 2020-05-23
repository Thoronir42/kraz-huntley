<?php declare(strict_types=1);

namespace SeStep\Typeful\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use SeStep\Typeful\Console\ListEntitiesCommand;
use SeStep\Typeful\Console\ListTypesCommand;
use SeStep\Typeful\Forms;
use SeStep\Typeful\Service;
use Symfony\Component\Console\Command\Command;

class TypefulExtension extends CompilerExtension
{
    use TypefulLoader;

    const TAG_TYPE = 'typeful.propertyType';
    const TAG_TYPE_CONTROL_FACTORY = 'typeful.typeControlFactory';
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

        $this->initTypeful($builder, $this->configFile['typeful']);

        if (class_exists(Command::class)) {
            $builder->addDefinition($this->prefix('listTypesCommand'))
                ->setType(ListTypesCommand::class)
                ->setArgument('name', $this->name . ':types:list');
            $builder->addDefinition($this->prefix('listDescriptorsCommand'))
                ->setType(ListEntitiesCommand::class)
                ->setArgument('name', $this->name . ':descriptors:list');
        }
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $types = [];
        $typesDefinitions = $builder->findByTag(self::TAG_TYPE);
        foreach (array_keys($typesDefinitions) as $typeServiceName) {
            $definition = $builder->getDefinition($typeServiceName);
            $types[$definition->getName()] = $definition;
        }

        /** @var ServiceDefinition $typeRegister */
        $typeRegister = $builder->getDefinition($this->prefix('typeRegister'));
        $typeRegister->setArgument('propertyTypes', $types);

        $propertyControlFactory = $builder->getDefinition($this->prefix('propertyControlFactory'));
        $propertyControlFactory->setArgument('typeMap', $builder->findByTag(self::TAG_TYPE_CONTROL_FACTORY));

        $descriptors = [];
        foreach ($builder->findByTag(self::TAG_ENTITY) as $service => $entityClass) {
            $descriptors[$entityClass] = $builder->getDefinition($service);
        }

        /** @var ServiceDefinition $entityDescriptorRegister */
        $entityDescriptorRegister = $builder->getDefinition($this->prefix('entityDescriptorRegister'));
        $entityDescriptorRegister->setArgument('descriptors', $descriptors);
    }
}
