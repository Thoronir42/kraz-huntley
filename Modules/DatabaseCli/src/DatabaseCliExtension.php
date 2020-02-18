<?php declare(strict_types=1);

namespace DatabaseCli;

use Contributte\Console\DI\ConsoleExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class DatabaseCliExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'initCmd' => Expect::structure([
                'files' => Expect::listOf(Expect::string()),
            ]),
            'wipeCmd' => Expect::structure([
                'schemaName' => Expect::string()->nullable(),
            ])
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $config = $this->getConfig();
        $initDatabaseCommand = $builder->addDefinition($this->prefix('initDatabaseCommand'))
            ->setType(InitDatabaseCommand::class);
        $initDatabaseCommand
            ->setArgument('defaultFiles', $this->createFilesIterator($config->initCmd->files))
            ->setArgument('commandName', 'db:init');

        if ($config->wipeCmd) {
            $builder->addDefinition($this->prefix('wipeDatabaseCommand'))
                ->setType(WipeSchemaCommand::class)
                ->setArgument('schemaName', $config->wipeCmd->schemaName)
                ->setArgument('commandName', 'db:wipe');
        }


    }

    private function createFilesIterator(array $files)
    {
        return new Statement(\ArrayIterator::class, [$files]);
    }
}
