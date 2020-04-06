<?php


namespace SeStep\Typeful\DI;


use Nette\DI\ContainerBuilder;
use Nette\Schema\ValidationException;
use PHPUnit\Framework\TestCase;
use SeStep\Typeful\Entity\GenericDescriptor;
use SeStep\Typeful\Forms\StandardControlsFactory;
use SeStep\Typeful\Types\IntType;
use SeStep\Typeful\Types\PropertyType;
use SeStep\Typeful\Types\TextType;

class TypefulLoaderDummyExtensionTest extends TestCase
{
    use TypefulLoader;

    public function testInitTypefulEntities()
    {
        $containerBuilder = new ContainerBuilder();

        $entities = [
            'producer' => [
                'name' => 'consoleProducer',
                'properties' => [
                    'name' => ['type' => 'text'],
                ]
            ],
            'console' => [
                'name' => 'gameConsole',
                'properties' => [
                    'platform' => ['type' => 'text'],
                    'version' => ['type' => 'int'],
                ],
            ],
        ];

        $this->initTypeful($containerBuilder, ['entities' => $entities]);

        $definitions = $containerBuilder->findByType(GenericDescriptor::class);
        self::assertCount(2, $definitions);
    }

    public function testInitTypefulMissingEntityName()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("producer › name' is missing");

        $containerBuilder = new ContainerBuilder();

        $entities = [
            'producer' => [
                'properties' => [
                    'name' => ['type' => 'text'],
                ]
            ],
        ];

        $this->initTypeful($containerBuilder, ['entities' => $entities]);
    }

    public function testInitTypefulTypes()
    {
        $types = [
            'number' => ['class' => IntType::class],
            'text' => ['class' => TextType::class, 'controlFactory' => StandardControlsFactory::class .'::createText'],
        ];

        $builder = new ContainerBuilder();

        $this->initTypeful($builder, ['types' => $types]);

        $definitions = $builder->findByType(PropertyType::class);
        self::assertCount(2, $definitions);

        $definitionsWithFactory = $builder->findByTag(TypefulExtension::TAG_TYPE_CONTROL_FACTORY);
        self::assertCount(1, $definitionsWithFactory);

    }

    public function prefix(string $name)
    {
        return "dummy.$name";
    }
}
