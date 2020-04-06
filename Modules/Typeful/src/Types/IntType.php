<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

class IntType implements PropertyType
{
    public function renderValue($value, array $options = [])
    {
        return $value;
    }
}
