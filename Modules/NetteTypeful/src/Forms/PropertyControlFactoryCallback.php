<?php declare(strict_types=1);

namespace SeStep\NetteTypeful\Forms;

use Nette\Forms\IControl;

abstract class PropertyControlFactoryCallback
{
    public function __invoke(string $name, array $options): IControl
    {
        return $this->create($name, $options);
    }

    abstract public function create(string $name, array $options): IControl;
}
