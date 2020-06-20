<?php declare(strict_types=1);

namespace SeStep\NetteTypeful\Forms;

use Nette\Forms\IControl;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use SeStep\Typeful\Entity\Property;

class PropertyControlFactory
{
    /** @var callable[] */
    private $typeToControlFactory = [];

    /**
     * @param callable[] $typeMap associative map of type to factory callback
     */
    public function __construct(array $typeMap)
    {
        foreach ($typeMap as $type => $callback) {
            $this->registerTypeFactoryCallback($type, $callback);
        }
    }

    public function getTypes(): array
    {
        $types = array_keys($this->typeToControlFactory);

        return array_combine($types, $types);
    }

    /**
     * @param string $type
     * @param PropertyControlFactoryCallback|callable $callback
     */
    public function registerTypeFactoryCallback(string $type, $callback): void
    {
        if (isset($this->typeToControlFactory[$type])) {
            throw new InvalidStateException("Type '$type' is already registered");
        }
        if (!is_callable($callback) && !$callback instanceof PropertyControlFactoryCallback) {
            throw new InvalidArgumentException("Parameter \$callback is not callable");
        }

        $this->typeToControlFactory[$type] = $callback;
    }

    public function create(string $label, string $type, array $typeOptions = []): IControl
    {
        $controlFactory = $this->typeToControlFactory[$type] ?? null;
        if (!$controlFactory) {
            throw new InvalidStateException("Type '$type' does not have a factory associated");
        }

        return $controlFactory($label, $typeOptions);
    }

    public function createByProperty(string $label, Property $property): IControl
    {
        return $this->create($label, $property->getType(), $property->getTypeOptions());
    }
}
