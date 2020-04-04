<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use Nette\Utils\Html;

class TextType implements PropertyType
{
    public function getName(): string
    {
        return 'text';
    }

    public function renderValue($value, array $options = [])
    {
        return $value;
    }
}
