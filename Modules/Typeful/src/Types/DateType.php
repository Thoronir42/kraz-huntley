<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use Latte\Runtime\Filters;
use Nette\NotImplementedException;
use SeStep\Typeful\Validation\ValidationError;

class DateType implements PropertyType
{
    public function renderValue($value, array $options = [])
    {
        // TODO: Avoid using internal class Filters
        return Filters::date($value, 'Y-m-d');
    }

    public function validateValue($value, array $options = []): ?ValidationError
    {
        throw new NotImplementedException();
    }
}
