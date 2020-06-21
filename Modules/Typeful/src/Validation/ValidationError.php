<?php declare(strict_types=1);

namespace SeStep\Typeful\Validation;

// todo: specify guts of the error
class ValidationError
{
    const INVALID_TYPE = 'typeful.error.invalidType';
    const INVALID_VALUE = 'typeful.error.invalidValue';
    const SURPLUS_FIELD = 'typeful.error.surplusField';
    const UNDEFINED_VALUE = 'typeful.error.undefinedValue';

    /** @var string */
    private $errorType;

    public function __construct(string $errorType)
    {
        $this->errorType = $errorType;
    }

    public function getErrorType(): string
    {
        return $this->errorType;
    }

}
