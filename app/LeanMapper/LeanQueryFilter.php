<?php declare(strict_types=1);

namespace App\LeanMapper;

use Dibi\Fluent;
use LeanMapper\Entity;
use LeanMapper\Exception\InvalidArgumentException;
use LeanMapper\IMapper;
use LeanMapper\Reflection\EntityReflection;

class LeanQueryFilter
{
    private static $CONDITIONS = [
        true => [
            'IN' => 'IN',
            'NULL' => 'IS NULL',
            'LIKE' => 'LIKE',
            'EQ' => '=',
        ],
        false => [
            'IN' => 'NOT IN',
            'NULL' => 'IS NOT NULL',
            'LIKE' => 'NOT LIKE',
            'EQ' => '!=',
        ],
    ];

    /** @var IMapper */
    private $mapper;

    public function __construct(IMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function apply(Fluent $fluent, array $conditions, string $entityClass)
    {
        if (!is_a($entityClass, Entity::class, true)) {
            throw new \InvalidArgumentException("$entityClass is not an entity class");
        }
        /** @var EntityReflection $reflection */
        $reflection = $entityClass::getReflection($this->mapper);

        foreach ($conditions as $property => $condition) {
            if ($property[0] === '!') {
                $property = substr($property, 1);
                $conditions = &self::$CONDITIONS[false];
            } else {
                $conditions = &self::$CONDITIONS[true];
            }
            $value = $this->normalizeValue($condition);

            $propertyRef = $reflection->getEntityProperty($property);
            if (!$propertyRef) {
                throw new InvalidArgumentException("Property '$property' does not exist");
            }
            $column = $propertyRef->getColumn();
            if (is_array($value)) {
                $fluent->where("$column $conditions[IN] %in", $value);
            } elseif (is_null($value)) {
                $fluent->where("$column $conditions[NULL]");
            } elseif (is_string($value) && strpos($value, '%') !== false) {
                $fluent->where("$column $conditions[LIKE] ?", $value);
            } else {
                $fluent->where("$column $conditions[EQ] %s", $value);
            }
        }
    }

    private function normalizeValue($value)
    {
        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $item) {
                $result[$key] = $this->normalizeValue($item);
            }

            return $result;
        }
        if ($value instanceof Entity) {
            return $value->id;
        }

        return $value;
    }
}
