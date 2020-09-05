<?php declare(strict_types=1);

namespace App\LeanMapper;

use Dibi\Expression;
use Dibi\Fluent;
use LeanMapper\Entity;
use LeanMapper\Exception\InvalidArgumentException;
use LeanMapper\IMapper;
use LeanMapper\Reflection\EntityReflection;
use Nette\Utils\Arrays;

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

            $propertyRef = $reflection->getEntityProperty($property);
            if (!$propertyRef) {
                throw new InvalidArgumentException("Property '$property' does not exist");
            }
            $column = $propertyRef->getColumn();

            if ($condition instanceof Expression) {
                $fluent->where($column, $condition);
                continue;
            }

            $value = $this->normalizeValue($condition);
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

    public function order(Fluent $fluent, array $order, string $entityClass)
    {
        if (empty($order)) {
            return;
        }

        /** @var EntityReflection $reflection */
        $reflection = $entityClass::getReflection($this->mapper);

        foreach ($order as $property => $direction) {
            if (is_int($property)) {
                $property = $direction;
                $direction = 'ASC';
            }

            $propertyReflection = $reflection->getEntityProperty($property);
            if (!$propertyReflection) {
                throw new InvalidArgumentException("Property '$property' does not exist");
            }
            if ($propertyReflection->hasRelationship()) {
                throw new InvalidArgumentException("Property '$property' has a relationship and cannot be" .
                    " used for ordering");
            }

            $column = $propertyReflection->getColumn();
            $fluent->orderBy("$column $direction");
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
