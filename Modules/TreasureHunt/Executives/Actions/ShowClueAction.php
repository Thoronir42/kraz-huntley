<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Actions;

use CP\TreasureHunt\Executives\NavigationResultBuilder;
use CP\TreasureHunt\Model\Service\NarrativesService;
use CP\TreasureHunt\Model\Service\NotebookService;
use CP\TreasureHunt\Model\Service\TreasureMapsService;
use CP\TreasureHunt\Typeful\Types\ClueType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Module\RelativeDate;
use SeStep\Executives\Validation\HasParamsSchema;
use SeStep\Executives\Validation\ParamValidationError;
use SeStep\Executives\Validation\ValidatesParams;

class ShowClueAction implements Action, HasParamsSchema, ValidatesParams
{
    use RelativeDate;

    /** @var ClueType */
    private $clueType;
    /** @var NarrativesService */
    private $narrativesService;
    /** @var TreasureMapsService */
    private $treasureMapsService;
    /** @var NotebookService */
    private $notebookService;

    public function __construct(
        ClueType $clueType,
        NarrativesService $narrativesService,
        TreasureMapsService $treasureMapsService,
        NotebookService $notebookService
    ) {
        $this->clueType = $clueType;
        $this->narrativesService = $narrativesService;
        $this->treasureMapsService = $treasureMapsService;
        $this->notebookService = $notebookService;
    }

    public function execute($context, $params): ExecutionResult
    {
        $persist = $params['persist'];
        $clueType = $params['clueType'];
        $clueArgs = $params['clueArgs'];

        if ($persist) {
            $expireOn = $persist === true ? null : $this->getDateFrom($persist);
            $this->notebookService->addClueRevelation($context->currentPage, $clueType, $clueArgs, $expireOn);
        }


        return NavigationResultBuilder::forward($clueType)
            ->withArg('clueArgs', $params['clueArgs'])
            ->build();
    }

    public function getParamsSchema(): Schema
    {
        return Expect::structure([
            'clueType' => Expect::anyOf(...array_keys($this->clueType->getTypes()))->required(),
            'clueArgs' => Expect::structure([])->otherItems()->required(),
            'persist' => Expect::anyOf(Expect::bool(), Expect::string())->default(false),
        ]);
    }

    public function validateParams(array $params): array
    {
        $clueType = $params['clueType'];
        $clueArgs = $params['clueArgs'];

        $errors = [];
        if ($params['persist'] && $params['persist'] !== true) {
            if ($this->getDateFrom($params['persist']) === null) {
                $errors['persist'] = new ParamValidationError('invalidValue', [
                    'value' => $params['persist'],
                ]);
            }
        }

        foreach ($this->validateClue($clueType, $clueArgs) as $field => $error) {
            $errors[$field] = $error;
        }

        return $errors;
    }

    private function validateClue(string $clueType, array $clueArgs)
    {
        switch ($clueType) {
            case ClueType::NARRATIVE:
                return $this->validateNarrativeArgs($clueArgs);
            case ClueType::MAP:
                return $this->validateMapArgs($clueArgs);

            default:
                return [
                    'clueType' => new ParamValidationError('invalidValue', [
                        'value' => $clueType,
                        'expectedValues' => [ClueType::NARRATIVE, ClueType::MAP],
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
                'clueArgs.map' => new ParamValidationError('appTreasureHunt.treasureMap.notFound'),
            ];
        }

        return [];
    }
}
