<?php declare(strict_types=1);

namespace SeStep\Executives\Model;

use SeStep\Executives\Model\ActionData;
use SeStep\Executives\Model\ConditionData;
use SeStep\Executives\Model\GenericConditionData;

class GenericActionData implements ActionData
{

    /** @var string */
    private $type;
    /** @var array */
    private $params;
    /** @var ConditionData[] */
    private $conditions;

    /**
     * @param string $type
     * @param array $params
     * @param ConditionData[] $conditions
     */
    public function __construct(string $type, array $params, array $conditions = [])
    {
        $this->type = $type;
        $this->params = $params;
        $this->conditions = $conditions;
    }

    /** @inheritDoc */
    public function getType(): string
    {
        return $this->type;
    }

    /** @inheritDoc */
    public function getParams(): array
    {
        return $this->params;
    }

    /** @inheritDoc */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param mixed[] $data
     * @return GenericActionData[]
     */
    public static function createManyFrom(array $data): array
    {
        $actions = [];
        foreach ($data as $i => $action) {
            $actions[$i] = new GenericActionData($action['type'], $action['params'] ?? null,
                GenericConditionData::createManyFrom($action['conditions'] ?? []));
        }

        return $actions;
    }
}
