<?php declare(strict_types=1);

namespace SeStep\Typeful\Entity;

class Property
{
    /** @var string */
    private $type;
    /** @var array */
    private $typeOptions;

    public function __construct(string $type, array $typeOptions = [])
    {
        $this->type = $type;
        $this->typeOptions = $typeOptions;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTypeOptions(): array
    {
        return $this->typeOptions;
    }

    public function isNullable(): bool
    {
        return $this->typeOptions['nullable'] ?? false;
    }
}
