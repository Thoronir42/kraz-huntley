<?php declare(strict_types=1);

namespace SeStep\LeanExecutives\Entity;

use LeanMapper\Entity;
use SeStep\Executives\Model\ActionData;

/**
 * Class Action
 *
 * @property string $id
 * @property string $type
 * @property array $params
 *
 * @property Condition[] $conditions m:hasMany(action_id:exe__action_has_condition:condition_id)
 */
class Action extends Entity implements ActionData
{
    private $arrayParams = null;

    /**
     * @inheritDoc
     */
    public function getConditions(): array
    {
        // todo fix condition data
        return $this->get('conditions');
    }

    public function getType(): string
    {
        return $this->get('type');
    }

    public function getParams(): array
    {
        if ($this->arrayParams === null) {
            $this->arrayParams = json_decode($this->row->params);
        }

        return $this->arrayParams;
    }

    public function setParams(array $params)
    {
        $this->arrayParams = $params;

        $this->row->params = json_encode($params);
    }

    public static function createFrom(ActionData $data): Action
    {
        $action = new Action();
        $action->type = $data->getType();
        $action->params = $data->getParams();

        return $action;
    }
}
