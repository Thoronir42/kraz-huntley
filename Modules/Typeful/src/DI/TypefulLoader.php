<?php declare(strict_types=1);

namespace SeStep\Typeful\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use SeStep\NetteTypeful\DI\NetteTypefulExtension;
use SeStep\Typeful\Entity\GenericDescriptor;
use SeStep\Typeful\Entity\Property;

/**
 * TypefulLoader helps {@link CompilerExtension} instances with loading of typeful declarations
 */
trait TypefulLoader
{
    private static $typefulSchema;

    private static function getTypefulSchema()
    {
        if (!self::$typefulSchema) {
            $typeSchema = Expect::anyOf(
                Expect::structure([
                    'class' => Expect::string()->required(),
                    'arguments' => Expect::array(),
                    'autowired' => Expect::bool(false),
                    'netteControlFactory' => Expect::mixed(),
                ]),
                Expect::structure([
                    'service' => Expect::string()->assert(function ($value) {
                        return mb_substr($value, 0, 1) === '@';
                    }, 'String is in a service reference format'),
                    'netteControlFactory' => Expect::mixed(),
                ]),
            );
            $entitySchema = Expect::structure([
                'name' => Expect::string(),
                'propertyNamePrefix' => Expect::string(),
                'properties' => Expect::arrayOf(Expect::structure([
                    'type' => Expect::string()->required(),
                    'options' => Expect::array(),
                ]))->min(1.0)
            ]);

            self::$typefulSchema = Expect::structure([
                'types' => Expect::arrayOf($typeSchema),
                'entities' => Expect::arrayOf($entitySchema),
            ]);
        }

        return self::$typefulSchema;
    }

    /**
     * @param array $entities
     * @return mixed
     *
     * @internal
     */
    private static function processConfig(array &$config)
    {
        $processor = new Processor();
        return $processor->process(self::getTypefulSchema(), $config);
    }

    /**
     * Loads typeful declarations into given container builder
     *
     * @param ContainerBuilder $builder
     * @param array $typeful
     */
    protected function initTypeful(ContainerBuilder $builder, array $typeful): void
    {
        $config = self::processConfig($typeful);

        foreach ($config->types as $type => $definition) {
            if (isset($definition->service)) {
                $service = mb_substr($definition->service, 1);
                $typeDefinition = $builder->getDefinition($service);
            } else {
                $typeDefinition = $builder->addDefinition($this->prefix("type.$type"))
                    ->setType($definition->class)
                    ->setAutowired($definition->autowired)
                    ->setArguments($definition->arguments);
            }
            $typeDefinition->addTag(TypefulExtension::TAG_TYPE, $this->prefix($type));

            if (isset($definition->netteControlFactory)) {
                $typeDefinition->addTag(NetteTypefulExtension::TAG_TYPE_CONTROL_FACTORY,
                    $definition->netteControlFactory);
            }
        }

        foreach ($config->entities as $entity => $definition) {
            $builder->addDefinition(
                $this->prefix("entity.$entity"),
                $this->createEntityDefinition($definition)
                    ->addTag(TypefulExtension::TAG_ENTITY, $definition->name ?? $entity)
            );
        }
    }

    private function createEntityDefinition($definition)
    {
        return (new ServiceDefinition())
            ->setType(GenericDescriptor::class)
            ->setAutowired(false)
            ->setArguments([
                'properties' => self::getPropertiesStatement($definition->properties),
                'propertyNamePrefix' => $definition->propertyNamePrefix ?? '',
            ]);
    }

    protected static function getPropertiesStatement(array $properties): array
    {
        $propertyStatements = [];
        foreach ($properties as $name => $property) {
            $propertyStatements[$name] = new Statement(Property::class,
                [$property->type, $property->options ?? []]);
        }

        return $propertyStatements;
    }
}
