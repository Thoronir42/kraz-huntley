<?php declare(strict_types=1);

namespace SeStep\Executives\Module\Actions;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\Executives\Module\MultiActionStrategyFactory;
use SeStep\Executives\Model\GenericActionData;
use SeStep\Executives\Execution\ActionExecutor;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\ExecutionResult;
use SeStep\Executives\Validation\ExecutivesValidator;
use SeStep\Executives\Validation\HasParamsSchema;
use SeStep\Executives\Validation\ValidatesParams;

class MultiAction implements Action, HasParamsSchema, ValidatesParams
{
    /** @var MultiActionStrategyFactory */
    private $strategyFactory;
    /** @var ActionExecutor */
    private $actionExecutor;
    /** @var ExecutivesValidator */
    private $validator;

    public function __construct(
        MultiActionStrategyFactory $strategyFactory,
        ActionExecutor $actionExecutor,
        ExecutivesValidator $validator
    ) {
        $this->strategyFactory = $strategyFactory;
        $this->actionExecutor = $actionExecutor;
        $this->validator = $validator;
    }

    public function execute($context, $params): ExecutionResult
    {
        $strategy = $this->strategyFactory->create($params['strategy']);

        $actionResults = [];

        foreach (GenericActionData::createManyFrom($params['actions']) as $i => $actionData) {
            $result = $this->actionExecutor->execute($actionData, $context);
            $actionResults[$i] = $result;

            $strategyResult = $strategy->onPartialResult($result, $actionResults);
            if ($strategyResult) {
                return $strategyResult;
            }
        }

        return $strategy->onAllActionsDone($actionResults);
    }


    public function getParamsSchema(): Schema
    {
        return Expect::structure([
            'strategy' => Expect::anyOf(...$this->strategyFactory->listStrategies()),
            'actions' => Expect::listOf(Expect::structure([
                'type' => Expect::string()->required(),
                'params' => Expect::structure([])->otherItems()->castTo('array'),
                'conditions' => Expect::listOf(Expect::structure([
                    'type' => Expect::string()->required(),
                    'params' => Expect::structure([])->otherItems()->castTo('array'),
                ])->castTo('array')),
            ])->castTo('array'))->required(),
        ]);
    }

    public function validateParams(array $params): array
    {
        $result = [];

        // TODO: See if there is better way of composing error list
        foreach ($params['actions'] as $iAct => $action) {
            $actionValidator = $this->validator->withPath("actions[$iAct]");
            $actionErrors = iterator_to_array($actionValidator->validateActionData($action));
            $result += $actionErrors;

            foreach ($action['conditions'] ?? [] as $iCond => $conditionData) {
                $conditionValidator = $actionValidator->withPath("conditions[$iCond]");
                $result += iterator_to_array($conditionValidator->validateConditionData($conditionData));
            }
        }

        return $result;
    }
}
