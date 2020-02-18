<?php declare(strict_types=1);

namespace CP\TreasureHunt;

use Nette\DI\CompilerExtension;

class TreasureHuntExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $file = $this->loadFromFile(__DIR__ . '/treasureHuntExtension.neon');
        $this->loadDefinitionsFromConfig($file['services']);
    }
}
