<?php declare(strict_types=1);

namespace SeStep\Executives\DI;


use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\InvalidStateException;

class ExecutivesExtension extends CompilerExtension
{
    const TAG_EXECUTIVE_MODULE = 'executiveModule';

    public function loadConfiguration()
    {
        $file = $this->loadFromFile(__DIR__ . '/executivesExtension.neon');
        $this->loadDefinitionsFromConfig($file['services']);
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
        $actionsService = $builder->getDefinition($this->prefix('actionsService'));
        $actionsService->setArgument('modules', $modules);
    }
}
