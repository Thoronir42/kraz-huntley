<?php declare(strict_types=1);

namespace CP\TreasureHunt;

use Nette\DI\CompilerExtension;
use SeStep\Typeful\DI\TypefulLoader;

class TreasureHuntExtension extends CompilerExtension
{
    use TypefulLoader;

    public function loadConfiguration()
    {
        $file = $this->loadFromFile(__DIR__ . '/treasureHuntExtension.neon');
        $this->loadDefinitionsFromConfig($file['services']);

        $this->initTypeful($this->getContainerBuilder(), $file['typeful']);
    }
}
