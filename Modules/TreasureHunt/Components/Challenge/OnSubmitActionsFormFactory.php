<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Challenge;

use SeStep\Executives\Model\ActionData;

interface OnSubmitActionsFormFactory
{
    public function create(ActionData $action = null): OnSubmitActionsForm;
}
