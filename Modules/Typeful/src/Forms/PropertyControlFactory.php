<?php declare(strict_types=1);

namespace SeStep\Typeful\Forms;

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

    public function create(Property $property): IControl
    {
        $type = $property->getType();
        $controlFactory = $this->typeToControlFactory[$type] ?? null;
        if (!$controlFactory) {
            throw new InvalidStateException("Type '$type' does not have a factory associated");
        }

        return $controlFactory($property->getName(), $property->getTypeOptions());
    }
}
