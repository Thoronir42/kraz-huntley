<?php


namespace SeStep\NetteTypeful\Service;


use Nette\Schema\Expect;
use PHPUnit\Framework\TestCase;

class SchemaConverterTest extends TestCase
{
    private function createInstance(): SchemaConverter
    {
        return new SchemaConverter();
    }

    public function testConvertString()
    {
        $typeful = $this->createInstance()->schemaToTypeful(Expect::string());

        self::assertEquals(['type' => 'typeful.text', 'options' => []], $typeful);
    }

    public function testConvertInt()
    {
        $expected = ['type' => 'typeful.int', 'options' => []];

        self::assertEquals($expected, $this->createInstance()->schemaToTypeful(Expect::int()));
        $expected['options'] = ['min' => 4, 'max' => 20];

        self::assertEquals($expected, $this->createInstance()->schemaToTypeful(Expect::int()->min(4)->max(20)));
    }

    public function testConvertList()
    {
        $typeful = $this->createInstance()->schemaToTypeful(Expect::list());

        $expected = [
            'type' => 'typeful.list',
            'options' => [
                'innerType' => null,
            ],
        ];

        self::assertEquals($expected, $typeful);

        $typeful = $this->createInstance()->schemaToTypeful(Expect::listOf('string'));

        $expected['options']['innerType'] = [
            'type' => 'typeful.text',
            'options' => [],
        ];

        self::assertEquals($expected, $typeful);
    }

    public function testConvertAnyOf()
    {
        $converter = $this->createInstance();

        $typeful = $converter->schemaToTypeful(Expect::anyOf('a', 'b', 'c'));

        $expected = [
            'type' => 'typeful.selection',
            'options' => [
                'items' => ['a', 'b', 'c'],
            ],
        ];

        self::assertEquals($expected, $typeful);
    }
}
