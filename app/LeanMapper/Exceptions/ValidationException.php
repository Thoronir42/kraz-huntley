<?php declare(strict_types=1);

namespace App\LeanMapper\Exceptions;

use SeStep\Typeful\Validation\ValidationError;

class ValidationException extends \RuntimeException
{
    /** @var string[] */
    private $errors;

    public function __construct(string $message, array $errors)
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    /**
     * @param ValidationError[] $errors
     */
    public static function fromTypefulValidationErrors(array $errors)
    {
        $exceptionErrors = [];
        foreach ($errors as $field => $error) {
            $exceptionErrors[$field] = $error->getErrorType();
        }

        return new self('Persistence layer validation failed', $exceptionErrors);
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
