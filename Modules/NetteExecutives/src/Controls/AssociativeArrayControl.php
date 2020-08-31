<?php declare(strict_types=1);

namespace SeStep\NetteExecutives\Controls;

use Nette\Forms\Controls\TextArea;
use Nette\Forms\Form;
use Nette\InvalidArgumentException;
use Nette\Neon\Neon;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class AssociativeArrayControl extends TextArea
{
    public function __construct($label = null)
    {
        parent::__construct($label);

        $this->controlPrototype->class[] = 'executives-config';
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

        try {
            $this->setValue(Json::decode($httpData, Json::FORCE_ARRAY));
        } catch (JsonException $ex) {
            $this->addError('JSON parse failed', false);
        }
    }

    protected function getRenderedValue(): ?string
    {
        return str_replace("\t", '  ', Json::encode($this->value, Json::PRETTY));
    }


}
