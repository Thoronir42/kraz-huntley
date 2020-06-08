<?php declare(strict_types=1);

namespace SeStep\Executives\Test;

use SeStep\Executives\ExecutivesModule;

class ArithmeticsTestModule implements ExecutivesModule
{
    public function getLocalizationName(): string
    {
        return 'arithmetics';
    }

    public function getActions(): array
    {
        return [
            'add' => Arithmetics\Add::class,
            'divide' => Arithmetics\Divide::class,
        ];
    }

    public function getConditions(): array
    {
        return [
            'parity' => Arithmetics\ParityCondition::class,
        ];
    }
}
