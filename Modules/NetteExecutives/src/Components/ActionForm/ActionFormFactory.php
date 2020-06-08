<?php declare(strict_types=1);

namespace SeStep\NetteExecutives\Components\ActionForm;

use SeStep\Executives\Model\ActionData;

interface ActionFormFactory
{
    public function create(ActionData $action = null): ActionForm;
}
