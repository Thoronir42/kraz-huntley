<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Repository;

use App\LeanMapper\Repository;
use CP\TreasureHunt\Model\Entity\Action;
use CP\TreasureHunt\Model\Entity\ActionCondition;

class ActionConditionRepository extends Repository
{

    /**
     * @param Action $action
     * @param ActionCondition[] $conditions
     */
    public function createConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            $this->persist($condition);
        }
    }

    public function deleteByAction(Action $action): int
    {
        $delete = $this->connection->delete($this->getTable());
        $this->filter->apply($delete, ['action' => $action], ActionCondition::class);

        return $delete->execute();

    }
}
