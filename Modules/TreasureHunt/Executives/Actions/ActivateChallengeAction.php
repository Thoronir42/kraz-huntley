<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Actions;

use CP\TreasureHunt\Model\Service\ChallengesService;
use CP\TreasureHunt\Model\Service\NotebookService;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;
use SeStep\Executives\Model\ActionData;
use SeStep\Executives\Model\GenericActionData;
use SeStep\Executives\Validation\HasParamsSchema;

class ActivateChallengeAction implements Action, HasParamsSchema
{
    /** @var NotebookService */
    private $notebookService;
    /** @var ChallengesService */
    private $challengesService;

    public function __construct(NotebookService $notebookService, ChallengesService $challengesService)
    {
        $this->challengesService = $challengesService;
        $this->notebookService = $notebookService;
    }

    public function execute($context, $params): ExecutionResult
    {
        $notebook = $context->notebook;

        $challenge = $this->challengesService->getChallenge($params['challengeId']);
        if (!$challenge) {
            return ExecutionResultBuilder::fail(ExecutionResult::CODE_EXECUTION_FAILED, 'th.challengeNotFound')
                ->create();
        }

        $result = $this->notebookService->activateChallengePage($notebook, $challenge);

        if (!$result) {
            return ExecutionResultBuilder::fail(ExecutionResult::CODE_EXECUTION_FAILED, 'th.pageActivationFailed', [
                'challengeId' => $params['challengeId'],
            ])
                ->create();
        }

        return ExecutionResult::ok([]);
    }

    public static function create(): ActionData
    {
        return new GenericActionData('exe.multiAction', [
            'strategy' => 'executeAll',
            'actions' => [],
        ]);
    }

    public function getParamsSchema(): Schema
    {
        $challengeIds = array_keys($this->challengesService->getNames());

        return Expect::structure([
            'challengeId' => Expect::anyOf(...$challengeIds)->required(),
        ]);
    }
}
