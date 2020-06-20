<?php declare(strict_types=1);

namespace SeStep\Typeful\TestDoubles;

use SeStep\NetteTypeful\Forms\PropertyControlFactory;
use SeStep\NetteTypeful\Forms\StandardControlsFactory;

abstract class PropertyFactory
{
    public static function createControlFactory(): PropertyControlFactory
    {
        return new PropertyControlFactory([
            'text' => [StandardControlsFactory::class, 'createText'],
            'int' => [StandardControlsFactory::class, 'createInt'],
        ]);
    }
}
