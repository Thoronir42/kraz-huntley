<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Actions;

use CP\TreasureHunt\Executives\NavigationResultBuilder;
use CP\TreasureHunt\Model\Service\NarrativesService;
use CP\TreasureHunt\Typeful\Types\ClueType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Execution\ExecutionResultBuilder;
use SeStep\Executives\Validation\HasParamsSchema;
use SeStep\Executives\Validation\ParamValidationError;
use SeStep\Executives\Validation\ValidatesParams;

class ShowClueAction implements Action, HasParamsSchema, ValidatesParams
{
    /** @var ClueType */
    private $clueType;
    /** @var NarrativesService */
    private $narrativesService;

    public function __construct(ClueType $clueType, NarrativesService $narrativesService)
    {
        $this->clueType = $clueType;
        $this->narrativesService = $narrativesService;
    }

    public function execute($context, $params): ExecutionResult
    {
        return NavigationResultBuilder::forward($params['clueType'])
            ->withArg('clueArgs', $params['clueArgs'])
            ->build();
    }

    public function getParamsSchema(): Schema
    {
        return Expect::structure([
            'clueType' => Expect::anyOf(...array_keys($this->clueType->getTypes()))->required(),
            'clueArgs' => Expect::array()->required(),
        ]);
    }

    public function validateParams(array $params): array
    {
        $clueType = $params['clueType'];
        $clueArgs = $params['clueArgs'];

        switch ($clueType) {
            case ClueType::NARRATIVE:
                return $this->validateNarrativeArgs($clueArgs);

            default:
                return [
                    'clueType' => new ParamValidationError('invalidValue', [
                        'value' => $clueType,
                        'expectedValues' => [ClueType::NARRATIVE],
                    ]),
                ];
        }
    }

    private function validateNarrativeArgs(array $args): array
    {
        if (!isset($args['narrative'])) {
            return [
                'clueArgs.narrative' => new ParamValidationError('schema.optionMissing'),
            ];
        }

        $narrative = $this->narrativesService->getNarrative((string)$args['narrative']);
        if (!$narrative) {
            return [
                'clueArgs.narrative' => new ParamValidationError('appTreasureHunt.narrativeNotFound'),
            ];
        }

        return [];
    }
}
