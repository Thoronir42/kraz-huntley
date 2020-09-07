<?php declare(strict_types=1);

namespace SeStep\NetteTypeful\Forms;

use LeanMapper\Exception\InvalidValueException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Controls\UploadControl;
use Nette\InvalidArgumentException;
use SeStep\NetteTypeful\Types\FileType;
use SeStep\Typeful\Types as TypefulTypes;

class StandardControlsFactory
{
    public static function createText(string $label, TypefulTypes\TextType $type, array $options)
    {
        if (isset($options['richText']) && $options['richText']) {
            $richText = $options['richText'];
            $control = new TextArea($label);
            $control->getControlPrototype()->class[] = $richText === true ? 'richtext' : $richText;
        } else {
            $control = new TextInput($label);
        }

        if (isset($options['maxLength'])) {
            $control->setMaxLength($options['maxLength']);
        }

        return $control;
    }

    public static function createInt(string $label, TypefulTypes\IntType $intType, array $options)
    {
        $control = new TextInput($label);
        $control->setHtmlType('number');
        self::assignAttributes($control, $options, ['min', 'max', 'step']);

        return $control;
    }

    public static function createFile(string $label, FileType $fileType, array $options)
    {
        $uploadControl = new UploadControl($label);
        if (isset($options['fileType'])) {
            if ($options['fileType'] === 'image') {
                $uploadControl->addRule(Form::IMAGE);
            } else {
                throw new InvalidArgumentException("fileType option '$options[fileType]' invalid ");
            }
        }

        return $uploadControl;
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
