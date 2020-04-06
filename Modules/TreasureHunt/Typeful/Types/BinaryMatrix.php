<?php declare(strict_types=1);

namespace CP\TreasureHunt\Typeful\Types;

use CP\TreasureHunt\Typeful\Controls\BinaryMatrixContainer;
use Nette\NotImplementedException;
use SeStep\Typeful\Types\PropertyType;

// TODO: Implement binary matrix control
class BinaryMatrix implements PropertyType
{
    /**
     * @inheritDoc
     */
    public function renderValue($value, array $options = [])
    {
        throw new NotImplementedException();
    }

    public static function createControl($name, array $options)
    {
        return new BinaryMatrixContainer($options['rows']);
    }
}
