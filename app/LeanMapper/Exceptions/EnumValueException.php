<?php declare(strict_types=1);

namespace App\LeanMapper\Exceptions;

use InvalidArgumentException;

class EnumValueException extends InvalidArgumentException
{
    public function __construct($value, array &$values)
    {
        $message = "$value is not valid value of " . implode(", ", $values);
        parent::__construct($message);
    }
}
