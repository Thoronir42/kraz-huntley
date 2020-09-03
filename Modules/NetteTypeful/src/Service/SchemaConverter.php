<?php declare(strict_types=1);

namespace SeStep\NetteTypeful\Service;

use Nette\Schema\Elements;
use Nette\Schema\Schema;

class SchemaConverter
{
    public function schemaToTypeful(Schema $schema)
    {
        if ($schema instanceof Elements\Structure) {
            return $this->convertStructure($schema);
        }
        if ($schema instanceof Elements\AnyOf) {
            return $this->convertAnyOf($schema);
        }
        if ($schema instanceof Elements\Type) {
            return $this->convertType($schema);
        }

        trigger_error("Unrecognized schema type: " . get_class($schema));
        return $this->getUnspecifiedType();
    }

    private function convertStructure(Elements\Structure $structure)
    {
        $structureData = [];
        foreach ($this->getPrivateProperty($structure, 'items') as $name => $item) {
            $structureData[$name] = $this->schemaToTypeful($item);
        }

        return [
            'type' => 'typeful.structure',
            'options' => [
                'structure' => $structureData
            ],
        ];
    }

    private function convertAnyOf(Elements\AnyOf $anyOf)
    {
        $items = $this->getPrivateProperty($anyOf, 'set');

        foreach ($items as $item) {
            if (is_object($item)) {
                return $this->getUnspecifiedType();
            }
        }

        return [
            'type' => 'typeful.selection',
            'options' => [
                'items' => $items,
            ],
        ];
    }

    private function convertType(Elements\Type $type)
    {
        $scalarType = $this->getPrivateProperty($type, 'type');

        switch ($scalarType) {
            case 'int':
            case 'float':
                [$min, $max] = $this->getPrivateProperty($type, 'range') + [null, null];
                $options = [];
                if (is_numeric($min)) {
                    $options['min'] = $min;
                }
                if (is_numeric($max)) {
                    $options['max'] = $max;
                }
                return [
                    'type' => 'typeful.' . $scalarType,
                    'options' => $options
                ];

            case 'array':
            case 'list':
                $innerType = $this->getPrivateProperty($type, 'items');

                return [
                    'type' => 'typeful.' . $scalarType,
                    'options' => [
                        'innerType' => $innerType ? $this->schemaToTypeful($innerType) : null,
                    ],
                ];

            case 'string':
                return [
                    'type' => 'typeful.text',
                    'options' => [],
                ];

            case 'mixed':
                // returns unspecified type
                break;

            default:
                trigger_error("Unrecognized type: " . $scalarType);
                break;
        }

        return $this->getUnspecifiedType();
    }

    private function getUnspecifiedType()
    {
        return [
            'type' => 'typeful.text',
            'options' => [],
        ];
    }

    private function getPrivateProperty($target, string $property, $fallback = null)
    {
        $accessor = \Closure::bind(function ($structure) use ($property, $fallback) {
            return isset($structure->$property) ? $structure->$property : $fallback;
        }, null, $target);

        return $accessor($target, $property);
    }
}
