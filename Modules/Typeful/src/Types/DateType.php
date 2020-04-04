<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use Latte\Runtime\Filters;

class DateType implements PropertyType
{
    public function getName(): string
    {
        return 'date';
    }

    public function renderValue($value, array $options = [])
    {
        // TODO: Avoid using internal class Filters
        return Filters::date($value, 'Y-m-d');
    }
}
