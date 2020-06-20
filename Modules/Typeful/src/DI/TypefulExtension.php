<?php declare(strict_types=1);

namespace SeStep\Typeful\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use SeStep\NetteTypeful\DI\NetteTypefulExtension;
use SeStep\Typeful\Console\ListEntitiesCommand;
use SeStep\Typeful\Console\ListTypesCommand;
use SeStep\Typeful\Service;
use SeStep\Typeful\Validation;
use Symfony\Component\Console\Command\Command;

class TypefulExtension extends CompilerExtension
{
    use TypefulLoader;

    const TAG_TYPE = 'typeful.propertyType';
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

        $typesDefinitions = $builder->findByTag(self::TAG_TYPE);
        foreach ($typesDefinitions as $definitionName => $typeName) {
            $definition = $builder->getDefinition($definitionName);
            $types[$typeName] = $definition;
        }

        /** @var ServiceDefinition $typeRegister */
        $typeRegister = $builder->getDefinition($this->prefix('typeRegister'));
        $typeRegister->setArgument('propertyTypes', $types);

        $descriptors = [];
        foreach ($builder->findByTag(self::TAG_ENTITY) as $service => $entityClass) {
            $descriptors[$entityClass] = $builder->getDefinition($service);
        }

        /** @var ServiceDefinition $entityDescriptorRegister */
        $entityDescriptorRegister = $builder->getDefinition($this->prefix('entityDescriptorRegister'));
        $entityDescriptorRegister->setArgument('descriptors', $descriptors);
    }
}
