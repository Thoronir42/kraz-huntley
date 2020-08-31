<?php declare(strict_types=1);

namespace SeStep\NetteExecutives;

use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use SeStep\NetteExecutives\Components;
use SeStep\NetteExecutives\Latte\ExecutivesLatteFilters;

class NetteExecutivesExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $filters = $builder->addDefinition($this->prefix('latteFilters'))
            ->setType(ExecutivesLatteFilters::class);

        $latteFactory = $builder->getDefinition('nette.latteFactory');
        $latteFactory->getResultDefinition()
            ->addSetup('addFilter', ['exeAction', [$filters, 'actionPlaceholder']])
            ->addSetup('addFilter', ['exeCondition', [$filters, 'conditionPlaceholder']]);
    }
}
