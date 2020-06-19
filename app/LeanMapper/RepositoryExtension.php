<?php declare(strict_types=1);


namespace App\LeanMapper;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use SeStep\EntityIds\IdGenerator;

class RepositoryExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        /** @var ServiceDefinition $definition */
        foreach ($builder->findByType(Repository::class) as $definition) {
            $definition->addSetup('$mapper = ?;', [$builder->getDefinition('leanMapper.mapper')]);
            $definition->addSetup('$entityClass = $mapper->getEntityClass($mapper->getTableByRepositoryClass(?))', [
                $definition->getType()
            ]);
            $definition->addSetup(<<<PHP
\$idGenerator = ?;
if(\$idGenerator->hasType(\$entityClass)) {
    \$service->bindIdGenerator(\$idGenerator);
}
PHP, [$builder->getDefinitionByType(IdGenerator::class)]);

            $definition->addSetup(<<<PHP
\$validator = ?;
if(\$validator->entityExists(\$entityClass)) {
    \$service->bindTypefulValidator(\$validator);
}
PHP, [$builder->getDefinition('typeful.validator')]);
        }
    }
}
