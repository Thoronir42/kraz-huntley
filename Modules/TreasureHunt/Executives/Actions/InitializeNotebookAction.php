<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Actions;

use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Entity\NotebookPage;
use CP\TreasureHunt\Model\Repository\NotebookPageRepository;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ClassnameActionExecutor;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;

class InitializeNotebookAction implements Action
{
    /** @var NotebookPageRepository */
    private $notebookPageRepository;
    /** @var ClassnameActionExecutor */
    private $classnameActionExecutor;

    public function __construct(
        NotebookPageRepository $notebookPageRepository,
        ClassnameActionExecutor $classnameActionExecutor
    ) {
        $this->notebookPageRepository = $notebookPageRepository;
        $this->classnameActionExecutor = $classnameActionExecutor;
    }

    public function execute($context, $params): ExecutionResult
    {
        /** @var Notebook $notebook */
        $notebook = $context->notebook;

        $indexPage = $this->notebookPageRepository->findOneBy([
            'notebook' => $notebook,
            'type' => NotebookPage::TYPE_INDEX,
        ]);
        if (!$indexPage) {
            $this->notebookPageRepository->createIndex($notebook);
        }

        $result = $this->activateChallenge($context, $params['challengeId']);
        bdump($result);

        return ExecutionResultBuilder::ok()
            ->create();
    }

    private function activateChallenge($context, string $challengeId): ExecutionResult
    {
        return $this->classnameActionExecutor->execute(ActivateChallengeAction::class, [
            'challengeId' => $challengeId,
        ], $context);
    }
}
