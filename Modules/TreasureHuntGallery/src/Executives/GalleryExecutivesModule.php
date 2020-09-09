<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery\Executives;


use SeStep\Executives\ExecutivesModule;

class GalleryExecutivesModule implements ExecutivesModule
{
    public function getLocalizationName(): string
    {
        return 'appTreasureHuntGallery';
    }

    public function getActions(): array
    {
        return [
            'unlockGallery' => Actions\UnlockGalleryAction::class,
        ];
    }

    public function getConditions(): array
    {
        return [];
    }
}
