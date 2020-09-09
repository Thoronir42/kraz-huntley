<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery\DI;


use Nette\DI\CompilerExtension;

class TreasureHuntGalleryExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $file = $this->loadFromFile(__DIR__ .'/treasureHuntGalleryExtension.neon');
        $this->loadDefinitionsFromConfig($file['services']);
    }

}
