<?php declare(strict_types=1);

namespace SeStep\NetteTypeful\Forms;

use Nette\Forms\Controls\HiddenField;
use Nette\Forms\IControl;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use SeStep\Typeful\Entity\Property;
use SeStep\Typeful\Service\TypeRegistry;

class PropertyControlFactory
{
    /** @var callable[] */
    private $typeToControlFactory = [];
    /** @var TypeRegistry */
    private $typeRegistry;

    /**
     * @param TypeRegistry $typeRegistry
     * @param callable[] $typeMap associative map of type to factory callback
     */
    public function __construct(TypeRegistry $typeRegistry, array $typeMap)
    {
        $this->typeRegistry = $typeRegistry;
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
        if (!$type) {
            return new HiddenField();
        }

        $controlFactory = $this->typeToControlFactory[$type] ?? null;
        if (!$controlFactory) {
            throw new InvalidStateException("Type '$type' does not have a factory associated");
        }

        $typeInstance = $this->typeRegistry->getType($type);

        return $controlFactory($label, $typeInstance, $typeOptions);
    }

    public function createByProperty(string $label, Property $property): IControl
    {
        $options = $property->getTypeOptions();
        $control = $this->create($label, $property->getType(), $options);

        if (method_exists($control, 'setRequired')) {
            $control->setRequired(true);
        }

        return $control;
    }
}
