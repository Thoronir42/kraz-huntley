<?php declare(strict_types=1);

namespace SeStep\Executives\Components\ActionForm;


use SeStep\Executives\Model\Entity\Action;

interface ActionFormFactory
{
    public function create(Action $action = null): ActionForm;
}
