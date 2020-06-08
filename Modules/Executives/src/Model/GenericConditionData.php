<?php declare(strict_types=1);

namespace SeStep\Executives\Model;

class GenericConditionData implements ConditionData
{
    /** @var string */
    private $type;
    /** @var array */
    private $params;

    /**
     * @param string $type
     * @param array $params
     */
    public function __construct(string $type, array $params)
    {
        $this->type = $type;
        $this->params = $params;
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

    /**
     * @param mixed[] $data
     * @return GenericConditionData[]
     */
    public static function createManyFrom(array $data): array
    {
        $conditions = [];
        foreach ($data as $i => $condition) {
            $conditions[$i] = new GenericConditionData($condition['type'], $condition['params']);
        }

        return $conditions;
    }
}
