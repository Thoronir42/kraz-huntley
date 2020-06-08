<?php declare(strict_types=1);

namespace SeStep\LeanExecutives\DI;

use Nette\DI\CompilerExtension;
use SeStep\LeanExecutives\ActionsService;
use SeStep\LeanExecutives\Repository\ActionRepository;
use SeStep\LeanExecutives\Repository\ConditionRepository;

class LeanExecutivesExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('actionRepository'))
            ->setType(ActionRepository::class);
        $builder->addDefinition($this->prefix('conditionRepository'))
            ->setType(ConditionRepository::class);

        $builder->addDefinition($this->prefix('actionsService'))
            ->setType(ActionsService::class);
    }
}
