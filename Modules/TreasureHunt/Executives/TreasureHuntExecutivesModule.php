<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives;


use CP\TreasureHunt\Executives\Actions\ActivateChallengeAction;
use CP\TreasureHunt\Executives\Actions\RevealNarrativeAction;
use CP\TreasureHunt\Executives\Conditions\AnswerEquals;
use SeStep\Executives\ExecutivesModule;

class TreasureHuntExecutivesModule implements ExecutivesModule
{
    private const ACTIONS = [
        'activateChallenge' => ActivateChallengeAction::class,
        'revealNarrative' => RevealNarrativeAction::class,
    ];
    private const CONDITIONS = [
        'answerEquals' => AnswerEquals::class,
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
