<?php declare(strict_types=1);

namespace SeStep\Executives\Execution;

class ExecutionResultBuilder
{
    /** @var int */
    private $code;
    /** @var array */
    private $data;

    public static function ok(): self
    {
        $builder = new ExecutionResultBuilder();
        $builder->code = ExecutionResult::CODE_OK;
        $builder->data = [];

        return $builder;
    }
    
    public static function fail(int $code, string $failReason, array $failParams = [])
    {
        $builder = new ExecutionResultBuilder();
        $builder->code = $code;

        $builder->data = [
            'fail' => [
                'reason' => $failReason,
                'params' => $failParams,
            ],
        ];

        return $builder;
    }

    public function update(string $field, $value)
    {
        if (!isset($this->data['update']) || !is_array($this->data['update'])) {
            $this->data['update'] = [];
        }

        $this->data['update'][$field] = $value;

        return $this;
    }

    public function create(): ExecutionResult
    {
        return new ExecutionResult($this->code, $this->data);
    }
}
