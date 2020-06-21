<?php declare(strict_types=1);

namespace SeStep\Typeful\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use SeStep\Typeful\Console\ListEntitiesCommand;
use SeStep\Typeful\Console\ListTypesCommand;
use SeStep\Typeful\Components;
use SeStep\Typeful\Forms;
use SeStep\Typeful\Service;
use SeStep\Typeful\Validation;
use Symfony\Component\Console\Command\Command;

class TypefulExtension extends CompilerExtension
{
    use TypefulLoader;

    const TAG_TYPE = 'typeful.propertyType';
    const TAG_TYPE_CONTROL_FACTORY = 'typeful.typeControlFactory';
    const TAG_ENTITY = 'typeful.entity';

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $configFile = $this->loadFromFile(__DIR__ . '/typefulExtension.neon');
        $this->initTypeful($builder, $configFile['typeful']);

        $this->loadDefinitionsFromConfig([
            'typeRegister' => Service\TypeRegistry::class,
            'entityDescriptorRegister' => Service\EntityDescriptorRegistry::class,
            'validator' => Validation\TypefulValidator::class,

            'propertyControlFactory' => Forms\PropertyControlFactory::class,
            'formPopulator' => Forms\EntityFormPopulator::class,

            'entityGridFactory' => Components\EntityGridFactory::class,
        ]);


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
        $controlFactories = [];
        $typesDefinitions = $builder->findByTag(self::TAG_TYPE);
        foreach ($typesDefinitions as $definitionName => $typeName) {
            $definition = $builder->getDefinition($definitionName);
            $types[$typeName] = $definition;
            $controlFactory = $definition->getTag(self::TAG_TYPE_CONTROL_FACTORY);
            if ($controlFactory) {
                $controlFactories[$typeName] = $controlFactory;
            }
        }

        /** @var ServiceDefinition $typeRegister */
        $typeRegister = $builder->getDefinition($this->prefix('typeRegister'));
        $typeRegister->setArgument('propertyTypes', $types);

        $propertyControlFactory = $builder->getDefinition($this->prefix('propertyControlFactory'));
        $propertyControlFactory->setArgument('typeMap', $controlFactories);

        $descriptors = [];
        foreach ($builder->findByTag(self::TAG_ENTITY) as $service => $entityClass) {
            $descriptors[$entityClass] = $builder->getDefinition($service);
        }

        /** @var ServiceDefinition $entityDescriptorRegister */
        $entityDescriptorRegister = $builder->getDefinition($this->prefix('entityDescriptorRegister'));
        $entityDescriptorRegister->setArgument('descriptors', $descriptors);
    }
}
