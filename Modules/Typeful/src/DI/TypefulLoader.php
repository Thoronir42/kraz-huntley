<?php declare(strict_types=1);

namespace SeStep\Typeful\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use SeStep\Typeful\Entity\GenericDescriptor;
use SeStep\Typeful\Entity\Property;

/**
 * TypefulLoader helps {@link CompilerExtension} instances with loading of typeful declarations
 */
trait TypefulLoader
{
    private static $entitiesSchema;

    private static function getEntitiesSchema()
    {
        if (!self::$entitiesSchema) {
            self::$entitiesSchema = Expect::arrayOf(Expect::structure([
                'name' => Expect::string()->required(),
                'propertyNamePrefix' => Expect::string(),
                'properties' => Expect::arrayOf(Expect::structure([
                    'type' => Expect::string()->required(),
                    'options' => Expect::array(),
                ]))->min(1.0),
            ]));
        }

        return self::$entitiesSchema;
    }

    /**
     * @param array $entities
     * @return mixed
     *
     * @internal
     */
    private static function processEntities(array &$entities)
    {
        $processor = new Processor();
        return $processor->process(self::getEntitiesSchema(), $entities);
    }

    /**
     * Loads typeful declarations into given container builder
     *
     * @param ContainerBuilder $builder
     * @param array $typeful
     */
    protected function initTypeful(ContainerBuilder $builder, array $typeful): void
    {
        foreach (self::processEntities($typeful['entities']) as $entity => $definition) {
            $builder->addDefinition($this->prefix("entity.$entity"))
                ->setType(GenericDescriptor::class)
                ->setAutowired(false)
                ->setArguments([
                    'name' => $definition->name,
                    'properties' => self::getPropertiesStatement($builder, $definition->properties),
                    'propertyNamePrefix' => $definition->propertyNamePrefix ?? '',
                ])
                ->addTag(TypefulExtension::TAG_ENTITY);
        }
    }

    protected static function getPropertiesStatement(ContainerBuilder $builder, array $properties): Statement
    {
        $propertyStatements = [];
        foreach ($properties as $name => $property) {
            $propertyStatements[] = new Statement(Property::class,
                [$name, $property->type, $property->options ?? []]);
        }

        return new Statement('[' . str_repeat('?, ', count($properties)) . ']', $propertyStatements);
    }
}
