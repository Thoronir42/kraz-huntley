<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Repository;

use App\LeanMapper\Repository;
use CP\TreasureHunt\Model\Entity\Action;

class ActionRepository extends Repository
{
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
