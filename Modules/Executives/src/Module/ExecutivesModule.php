<?php declare(strict_types=1);

namespace SeStep\Executives\Module;


class ExecutivesModule implements \SeStep\Executives\ExecutivesModule
{
    public function getLocalizationName(): string
    {
        return 'exe';
    }

    public function getActions(): array
    {
        return [
            'multiAction' => Actions\MultiAction::class,
        ];
    }

    public function getConditions(): array
    {
        return [
            'variableEquals' => Conditions\VariableEquals::class,
        ];
    }
}
