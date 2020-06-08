<?php declare(strict_types=1);

namespace SeStep\LeanExecutives\Entity;

use LeanMapper\Entity;
use SeStep\Executives\Model\ConditionData;

/**
 * @property string $id
 * @property string $type
 * @property array $params
 */
class Condition extends Entity implements ConditionData
{
    private $arrayParams = [];

    protected function initDefaults()
    {
        $this->setParams([]);
    }


    public function getType(): string
    {
        return $this->row->type;
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

    public static function createFrom(ConditionData $data)
    {
        $condition = new Condition();
        $condition->type = $data->getType();
        $condition->params = $data->getParams();

        return $condition;
    }
}
