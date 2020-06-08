<?php declare(strict_types=1);

namespace SeStep\Executives\Execution;

class ExecutionResult
{
    const CODE_OK = 0;
    const CODE_CONDITION_FAILED = 1;
    const CODE_EXECUTION_FAILED = 2;

    /** @var int */
    private $code;
    /** @var array */
    private $data;

    public function __construct(int $code, array $data)
    {
        $this->code = $code;
        $this->data = $data;
    }

    public function isOk(): bool
    {
        return $this->code === self::CODE_OK;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public static function ok(array $data)
    {
        return new self(self::CODE_OK, $data);
    }
}
