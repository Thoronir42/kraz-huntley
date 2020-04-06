<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use Nette\Utils\Html;

class TextType implements PropertyType
{
    public function renderValue($value, array $options = [])
    {
        return $value;
    }
}
