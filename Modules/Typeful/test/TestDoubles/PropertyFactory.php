<?php declare(strict_types=1);

namespace SeStep\Typeful\TestDoubles;

use SeStep\Typeful\Forms\PropertyControlFactory;
use SeStep\Typeful\Forms\StandardControlsFactory;

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
