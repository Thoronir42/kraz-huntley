<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Repository;

use App\LeanMapper\Repository;
use CP\TreasureHunt\Model\Entity\Action;

class ActionRepository extends Repository
{
    public function deleteConditions(Action $action)
    {
        $relationship = Action::getReflection($this->mapper)
            ->getEntityProperty('conditions')
            ->getRelationship();

        $sourcePrimary = $action->{$this->mapper->getPrimaryKey($this->mapper->getTable(get_class($action)))};
        $targetPrimaryColumn = $this->mapper->getPrimaryKey($relationship->getTargetTable());

        $targetIdsQuery = $this->connection->select($relationship->getColumnReferencingTargetTable())
            ->from($relationship->getRelationshipTable())
            ->where([$relationship->getColumnReferencingSourceTable() => $sourcePrimary]);
        $delete = $this->connection->delete($relationship->getTargetTable())
            ->where('%n IN ?', $targetPrimaryColumn, $targetIdsQuery);

        return $delete->execute();
    }

    protected function initEvents()
    {
        $this->events->registerCallback($this->events::EVENT_BEFORE_PERSIST, function ($action) {
            $this->ensureActionSequenceValid($action);
        });
    }

    private function ensureActionSequenceValid(Action $action)
    {
        if (!$action->sequence) {
            $action->sequence = $this->getNextInSequence('sequence', ['challenge' => $action->challenge], Action::class);
        }
    }
}
