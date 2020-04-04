<?php declare(strict_types=1);

namespace SeStep\Typeful\Forms;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;

class StandardControlsFactory
{
    public static function createText(string $name, array $options)
    {
        if (isset($options['richText'])) {
            $control = new TextArea($name);
            $control->getControlPrototype()->class[] = 'richtext';
        } else {
            $control = new TextInput($name);
        }

        if (isset($options['maxLength'])) {
            $control->setMaxLength($options['maxLength']);
        }

        return $control;
    }

    public static function createInt(string $name, array $options)
    {
        $control = new TextInput($name);
        $control->setHtmlType('number');
        self::assignAttributes($control, $options, ['min', 'max', 'step']);

        return $control;
    }

    private static function assignAttributes(BaseControl $control, array $options, $attributes)
    {
        foreach ($attributes as $target => $attribute) {
            if (is_numeric($target)) {
                $target = $attribute;
            }
            if (!isset($options[$attribute])) {
                continue;
            }

            $control->setHtmlAttribute($target, $options[$attribute]);
        }
    }
}
