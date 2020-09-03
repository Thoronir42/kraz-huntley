<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives;


use SeStep\Executives\ExecutivesModule;

class TreasureHuntExecutivesModule implements ExecutivesModule
{
    private const ACTIONS = [
        'activateChallenge' => Actions\ActivateChallengeAction::class,
        'revealClue' => Actions\ShowClueAction::class,
        'banAnswerSubmission' => Actions\BanAnswerSubmission::class,
    ];
    private const CONDITIONS = [
        'answerEquals' => Conditions\AnswerEquals::class,
        'answerCorrect' => Conditions\AnswerCorrect::class,
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
