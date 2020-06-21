<?php declare(strict_types=1);

namespace SeStep\Typeful\Entity;

class Property
{
    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var array */
    private $typeOptions;

    public function __construct(string $name, string $type, array $typeOptions = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->typeOptions = $typeOptions;
    }

    public function getName(): string
    {
        return $this->name;
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
