<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives;


use CP\TreasureHunt\Navigation;
use SeStep\Executives\Execution\ExecutionResult;

class NavigationResultBuilder
{
    private $data;

    private function __construct(string $advance, string $target)
    {
        $this->data = [
            Navigation::ADVANCE_TYPE => $advance,
            Navigation::TARGET => $target,
            Navigation::ARGS => [],
        ];
    }

    public function withArg(string $name, $value): self
    {
        $this->data[Navigation::ARGS][$name] = $value;
        return $this;
    }

    public function build(): ExecutionResult
    {
        return ExecutionResult::ok($this->data);
    }

    public static function redirect(string $target): self
    {
        return new self(Navigation::ADVANCE_REDIRECT, $target);
    }

    public static function forward(string $target): self
    {
        return new self(Navigation::ADVANCE_FORWARD, $target);
    }
}
