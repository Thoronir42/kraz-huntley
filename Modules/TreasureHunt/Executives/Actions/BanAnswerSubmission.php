<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Actions;


use CP\TreasureHunt\Model\Service\NotebookService;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;
use SeStep\Executives\Validation\HasParamsSchema;
use SeStep\Executives\Validation\ParamValidationError;
use SeStep\Executives\Validation\ValidatesParams;

class BanAnswerSubmission implements Action, HasParamsSchema, ValidatesParams
{
    /** @var NotebookService */
    private $notebookService;

    public function __construct(NotebookService $notebookService)
    {
        $this->notebookService = $notebookService;
    }

    public function execute($context, $params): ExecutionResult
    {
        $dateTime = new \DateTime($params['duration']);

        $this->notebookService->addInputBan($context->notebook->currentPage, $dateTime);

        return ExecutionResultBuilder::ok()
            ->update('activePage', $context->notebook->activePage)
            ->create();
    }

    public function getParamsSchema(): Schema
    {
        return Expect::structure([
            'duration' => Expect::string()->required(),
        ]);
    }

    public function validateParams(array $params): array
    {
        try {
            $date = new \DateTime($params['duration']);
            return [];
        } catch (\Throwable $exception) {
            return [
                'duration' => new ParamValidationError('invalidValue'),
            ];
        }
    }
}
