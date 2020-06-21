<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use Nette\Utils\Html;
use SeStep\Typeful\Validation\ValidationError;

interface PropertyType
{

    /**
     * @param mixed $value
     * @param array $options
     *
     * @return string|Html
     */
    public function renderValue($value, array $options = []);

    public function validateValue($value, array $options = []): ?ValidationError;
}
