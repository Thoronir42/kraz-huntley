<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

class IntType implements PropertyType
{
    public function getName(): string
    {
        return 'int';
    }

    public function renderValue($value, array $options = [])
    {
        return $value;
    }
}
