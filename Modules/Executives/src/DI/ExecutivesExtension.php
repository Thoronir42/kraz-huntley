<?php declare(strict_types=1);

namespace SeStep\Executives\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Container;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\InvalidStateException;
use SeStep\Executives\Execution\ActionExecutor;
use SeStep\Executives\Execution\ClassnameActionExecutor;
use SeStep\Executives\Execution\ExecutivesLocator;
use SeStep\Executives\ExecutivesLocalization;
use SeStep\Executives\Module\ExecutivesModule;
use SeStep\Executives\Module\MultiActionStrategyFactory;
use SeStep\Executives\ModuleAggregator;
use SeStep\Executives\Validation\ExecutivesValidator;

class ExecutivesExtension extends CompilerExtension
{
    const TAG_EXECUTIVE_MODULE = 'executivesModule';

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $this->loadDefinitionsFromConfig([
            'moduleAggregator' => ModuleAggregator::class,
            'validator' => ExecutivesValidator::class,
            'actionExecutor' => ActionExecutor::class,
        ]);

        $builder->addDefinition($this->prefix('executivesLocator'))
            ->setType(ExecutivesLocator::class)
            ->setArgument('resolveClosure', new Statement('Closure::fromCallable', [
                [$builder->getDefinitionByType(Container::class), 'createInstance'],
            ]));

        $builder->addDefinition($this->prefix('classnameActionExecutor'))
            ->setType(ClassnameActionExecutor::class)
            ->setArgument('resolveByClassname', new Statement('Closure::fromCallable', [
                [$builder->getDefinitionByType(Container::class), 'createInstance'],
            ]));

        $builder->addDefinition($this->prefix('executivesModule'))
            ->setType(ExecutivesModule::class)
            ->addTag(self::TAG_EXECUTIVE_MODULE, 'exe');

        $builder->addDefinition($this->prefix('localization'))
            ->setType(ExecutivesLocalization::class);
        $builder->addDefinition($this->prefix('multiActionStrategyFactory'))
            ->setType(MultiActionStrategyFactory::class);
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $modules = [];

        foreach ($builder->findByTag(self::TAG_EXECUTIVE_MODULE) as $name => $moduleName) {
            if (isset($modules[$moduleName])) {
                throw new InvalidStateException("Multiple modules of name '$moduleName' detected");
            }
            $modules[$moduleName] = $builder->getDefinition($name);
        }

        /** @var ServiceDefinition $actionsService */
        $actionsService = $builder->getDefinition($this->prefix('moduleAggregator'));
        $actionsService->setArgument('modules', $modules);
    }
}
