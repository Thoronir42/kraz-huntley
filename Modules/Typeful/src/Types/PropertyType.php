<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use Nette\Utils\Html;

interface PropertyType
{

    public function getName(): string;

    /**
     * @param mixed $value
     * @param array $options
     *
     * @return string|Html
     */
    public function renderValue($value, array $options = []);
}
