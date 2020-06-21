<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Actions;

use CP\TreasureHunt\Executives\NavigationResultBuilder;
use CP\TreasureHunt\Model\Service\NarrativesService;
use CP\TreasureHunt\Model\Service\TreasureMapsService;
use CP\TreasureHunt\Typeful\Types\ClueType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Validation\HasParamsSchema;
use SeStep\Executives\Validation\ParamValidationError;
use SeStep\Executives\Validation\ValidatesParams;

class ShowClueAction implements Action, HasParamsSchema, ValidatesParams
{
    /** @var ClueType */
    private $clueType;
    /** @var NarrativesService */
    private $narrativesService;
    /** @var TreasureMapsService */
    private $treasureMapsService;

    public function __construct(
        ClueType $clueType,
        NarrativesService $narrativesService,
        TreasureMapsService $treasureMapsService
    ) {
        $this->clueType = $clueType;
        $this->narrativesService = $narrativesService;
        $this->treasureMapsService = $treasureMapsService;
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
            case ClueType::MAP:
                return $this->validateMapArgs($clueArgs);

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

    private function validateMapArgs(array $args): array
    {
        if (!isset($args['map'])) {
            return [
                'clueArgs.map' => new ParamValidationError('schema.optionMissing'),
            ];
        }

        $map = $this->treasureMapsService->getMap($args['map']);
        if (!$map) {
            return [
                'clueArgs.map' => new ParamValidationError('appTreasureHunt.mapNotFound'),
            ];
        }
        
        return [];
    }
}
