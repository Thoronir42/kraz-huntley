<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives;


use CP\TreasureHunt\Executives\Actions\RevealChallengeAction;
use CP\TreasureHunt\Executives\Actions\RevealNarrativeAction;
use CP\TreasureHunt\Executives\Conditions\AnswerEquals;
use Nette\DI\Container;
use SeStep\Executives\ExecutivesModule;

class TreasureHuntExecutivesModule implements ExecutivesModule
{
    private const ACTIONS = [
        'activateChallenge' => RevealChallengeAction::class,
        'revealNarrative' => RevealNarrativeAction::class,
    ];
    private const CONDITIONS = [
        'answerEquals' => AnswerEquals::class,
    ];

    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function getActionTypes(): array
    {
        return array_keys(self::ACTIONS);
    }

    public function getConditionTypes(): array
    {
        return array_keys(self::CONDITIONS);
    }
}
