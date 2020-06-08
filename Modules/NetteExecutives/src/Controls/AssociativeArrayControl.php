<?php declare(strict_types=1);

namespace SeStep\NetteExecutives\Controls;

use Nette\Forms\Controls\TextArea;
use Nette\Forms\Form;
use Nette\InvalidArgumentException;
use Nette\Neon\Neon;

class AssociativeArrayControl extends TextArea
{
    public function __construct($label = null)
    {
        parent::__construct($label);

        $this->controlPrototype->class[] = 'neon';
    }

    public function setValue($value)
    {

        if ($value === null) {
            $value = [];
        } elseif (!is_array($value)) {
            throw new InvalidArgumentException(sprintf("Value must be array or null, %s given in field '%s'.",
                gettype($value), $this->name));
        }
        $this->value = $value;

        return $this;
    }


    /**
     * Returns control's value.
     * @return mixed
     */
    public function getValue()
    {
        return $this->value ?? [];
    }

    public function loadHttpData(): void
    {
        $httpData = $this->getHttpData(Form::DATA_TEXT);

        $this->setValue(Neon::decode($httpData));
    }

    protected function getRenderedValue(): ?string
    {
        return str_replace("\t", '  ', Neon::encode($this->value, Neon::BLOCK));
    }


}
