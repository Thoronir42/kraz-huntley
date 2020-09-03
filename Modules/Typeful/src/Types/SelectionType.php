<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;


use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use Nette\Utils\Arrays;
use SeStep\Typeful\Validation\ValidationError;

class SelectionType implements PropertyType
{
    public function renderValue($value, array $options = [])
    {
        return $value;
    }

    public function validateValue($value, array $options = []): ?ValidationError
    {
        if (!array_key_exists($value, $this->getValues())) {
            return new ValidationError(ValidationError::INVALID_VALUE);
        }

        return null;
    }

    private function getValues(array $options)
    {
        return $options['items'];
    }
}
