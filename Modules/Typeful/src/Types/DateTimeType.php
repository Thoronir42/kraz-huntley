<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use Latte\Runtime\Filters;

class DateTimeType implements PropertyType
{
    public function getName(): string
    {
        return 'datetime';
    }

    public function renderValue($value, array $options = [])
    {
        // TODO: Avoid using internal class Filters
        return Filters::date($value, 'Y-m-d H:i');
    }
}
