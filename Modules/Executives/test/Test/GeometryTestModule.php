<?php declare(strict_types=1);

namespace SeStep\Executives\Test;

use SeStep\Executives\ExecutivesModule;

class GeometryTestModule implements ExecutivesModule
{

    public function getLocalizationName(): string
    {
        return '';
    }

    public function getActions(): array
    {
        return [
            'squareSurface' => Geometry\SquareSurfaceAction::class
        ];
    }

    public function getConditions(): array
    {
        return [];
    }
}
