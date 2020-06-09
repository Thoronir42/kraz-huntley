<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives;

use CP\TreasureHunt\Executives as THExecutives;
use SeStep\Executives\ExecutivesModule;

class TreasureHuntExecutivesModule implements ExecutivesModule
{
    private const ACTIONS = [
        'activateChallenge' => THExecutives\Actions\ActivateChallengeAction::class,
        'revealClue' => THExecutives\Actions\ShowClueAction::class,
    ];
    private const CONDITIONS = [
        'answerEquals' => THExecutives\Conditions\AnswerEquals::class,
    ];

    public function getLocalizationName(): string
    {
        return 'appTreasureHunt';
    }

    public function getActions(): array
    {
        return self::ACTIONS;
    }

    public function getConditions(): array
    {
        return self::CONDITIONS;
    }
}
