<?php


namespace SeStep\Typeful\DI;


use Nette\DI\ContainerBuilder;
use Nette\Schema\ValidationException;
use PHPUnit\Framework\TestCase;
use SeStep\Typeful\Entity\GenericDescriptor;

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
        $this->expectExceptionMessage("'producer › name' is missing");

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

    public function prefix(string $name)
    {
        return "dummy.$name";
    }
}
