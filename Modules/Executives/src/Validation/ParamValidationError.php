<?php declare(strict_types=1);

namespace SeStep\Executives\Validation;

class ParamValidationError
{
    /** @var string */
    private $errorType;
    /** @var array */
    private $errorData;

    public function __construct(string $errorType, array $errorData = [])
    {
        $this->errorType = $errorType;
        $this->errorData = $errorData;
    }

    public function getErrorType(): string
    {
        return $this->errorType;
    }

    public function getErrorData(): array
    {
        return $this->errorData;
    }

    public function addData(string $field, $value, bool $overwrite = false): self
    {
        if ($overwrite || !isset($this->errorData[$field])) {
            $this->errorData[$field] = $value;
        }

        return $this;
    }
}
