<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use CP\TreasureHunt\Executives\NavigationResultBuilder;
use CP\TreasureHunt\Executives\Triggers\AnswerSubmitted;
use CP\TreasureHunt\Navigation;
use SeStep\Executives\Execution\ActionExecutor;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;
use stdClass;

class TreasureHuntService
{
    /** @var ActionExecutor */
    private $actionExecutor;

    public function __construct(ActionExecutor $actionExecutor)
    {
        $this->actionExecutor = $actionExecutor;
    }

    public function triggerSubmitAnswer(AnswerSubmitted $trigger): ExecutionResult
    {
        $challenge = $trigger->getChallenge();

        if (!$challenge->onSubmit) {
            return ExecutionResultBuilder::ok()
                ->withData('skipReason', 'noAction')
                ->withData('challengeId', $trigger->getChallenge()->id)
                ->withData('activePage', $trigger->getNotebook()->activePage)
                ->create();
        }

        $context = new stdClass();
        $context->notebook = $trigger->getNotebook();
        $context->challenge = $trigger->getChallenge();
        $context->answer = $trigger->getAnswer();

        $executionResult = $this->actionExecutor->execute($challenge->onSubmit, $context);
        if (!$executionResult->isOk()) {
            return $executionResult;
        }

        return NavigationResultBuilder::redirect(Navigation::TARGET_NOTEBOOK_PAGE)
            ->withArg('pageNumber', $context->activePage)
            ->build();
    }
}
