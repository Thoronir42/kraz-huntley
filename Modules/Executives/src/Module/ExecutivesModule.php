<?php declare(strict_types=1);

namespace SeStep\Executives\Module;

use SeStep\Executives\Module\Actions\MultiAction;

class ExecutivesModule implements \SeStep\Executives\ExecutivesModule
{
    public function getLocalizationName(): string
    {
        return 'exe';
    }

    public function getActions(): array
    {
        return [
            'multiAction' => MultiAction::class,
        ];
    }

    public function getConditions(): array
    {
        return [];
    }
}
