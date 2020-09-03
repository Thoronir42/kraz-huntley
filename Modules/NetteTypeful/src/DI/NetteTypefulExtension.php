<?php declare(strict_types=1);

namespace SeStep\NetteTypeful\DI;

use Nette\DI\CompilerExtension;
use Nette\InvalidStateException;
use SeStep\Typeful\DI\TypefulExtension;
use SeStep\Typeful\DI\TypefulLoader;
use SeStep\NetteTypeful\Components;
use SeStep\NetteTypeful\Forms;
use SeStep\NetteTypeful\Service;

class NetteTypefulExtension extends CompilerExtension
{
    use TypefulLoader;

    public const TAG_TYPE_CONTROL_FACTORY = 'netteTypeful.typeControlFactory';

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->loadFromFile(__DIR__ . '/netteTypefulExtension.neon');

        $this->initTypeful($builder, $config['typeful']);
        $this->loadDefinitionsFromConfig([
            'propertyControlFactory' => Forms\PropertyControlFactory::class,
            'formPopulator' => Forms\EntityFormPopulator::class,
            'entityGridFactory' => Components\EntityGridFactory::class,
            'schemaConverter' => Service\SchemaConverter::class,
        ]);
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $controlFactories = [];
        $controlFactoriesDefinitions = $builder->findByTag(self::TAG_TYPE_CONTROL_FACTORY);

        foreach ($controlFactoriesDefinitions as $name => $factory) {
            $definition = $builder->getDefinition($name);
            $typeName = $definition->getTag(TypefulExtension::TAG_TYPE);
            if (!$typeName) {
                throw new InvalidStateException("Service '$name' specifies a control factory but does not"
                    . " look like a type. Please make sure that the tag '" . TypefulExtension::TAG_TYPE . "' exists");
            }
            $controlFactories[$typeName] = $factory;
        }

        $propertyControlFactory = $builder->getDefinition($this->prefix('propertyControlFactory'));
        $propertyControlFactory->setArgument('typeMap', $controlFactories);
    }
}
